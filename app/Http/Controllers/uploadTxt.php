<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDeudoresBCRAFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class uploadTxt extends Controller
{

    public function uploadEntidades(Request $request)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file'
        ]);

        try {
            // donde guardo el archivo
            $uploadDir = storage_path('app/private/entidades/uploads');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('entidades/uploads');
            $fullPath = storage_path('app/private/' . $path);

            $file = new \SplFileObject($fullPath);
            $data = [];
            $batchSize = 1000;  // esto es la clave! carga de a poco para que no se sobrecargue

            while (!$file->eof()) {
                $line = trim($file->fgets());
                if ($line === '') continue;

                // convertir encoding
                $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8, ISO-8859-1, ASCII');

                $codigoEntidad = substr($line, 0, 5);
                $name_entidad  = trim(substr($line, 5));

                $data[] = [
                    'codigo_entidad' => $codigoEntidad,
                    'name_entidad'   => $name_entidad,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                if (count($data) >= $batchSize) {
                    DB::table('entidades_code')->upsert(
                        $data,
                        ['codigo_entidad'],
                        ['name_entidad', 'updated_at']
                    );
                    $data = [];
                }
            }

            if (!empty($data)) {
                DB::table('entidades_code')->upsert(
                    $data,
                    ['codigo_entidad'],
                    ['name_entidad', 'updated_at']
                );
            }

            // elimina el archivo asi no cargo el proyecto
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'message' => 'Archivo procesado y cargado en la DB'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Error procesando el archivo de entidades',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadClientes(Request $request)
    {
        ini_set('memory_limit', '4G');
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file'
        ]);

        try {
            $uploadDir = storage_path('app/private/clientes/uploads');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('clientes/uploads');
            $fullPath = storage_path('app/private/' . $path);

            $file = new \SplFileObject($fullPath);
            $data = [];
            $batchSize = 1000;

            while (!$file->eof()) {
                $line = trim($file->fgets());
                if ($line === '') continue;

                $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8, ISO-8859-1, ASCII');

                // Extraer campos
                $cuit          = substr($line, 0, 11);
                $name_cliente  = trim(substr($line, 11));

                $data[] = [
                    'cuit'          => $cuit,
                    'name_cliente'  => $name_cliente,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];

                if (count($data) >= $batchSize) {
                    DB::table('clientes')->upsert(
                        $data,
                        ['cuit'],
                        ['name_cliente', 'updated_at']
                    );
                    $data = [];
                }
            }

            if (!empty($data)) {
                DB::table('clientes')->upsert(
                    $data,
                    ['cuit'],
                    ['name_cliente', 'updated_at']
                );
            }

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'message' => 'Archivo de clientes procesado e insertado con éxito'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Error procesando el archivo de clientes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadFileDeudoresBCRA(Request $request)
    {
        ini_set('memory_limit', '6G');
        set_time_limit(0);

        $request->validate([
            'file' => 'required|file',
            'file_part' => 'required|integer',
            'file_name' => 'required|string',
            'file_finish' => 'required|boolean',
            'notify_email' => 'required|string',
        ]);

        try {
            $uploadDir = storage_path('app/private/deudores/uploads');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadedFile = $request->file('file');

            // el nombre y la parte recibida
            $originalName = $request->input('file_name') ?? $uploadedFile->getClientOriginalName();
            $filePart = (int) $request->input('file_part', 1);

            // guarda con nombre unico
            $extension = $uploadedFile->getClientOriginalExtension();
            $basename = pathinfo($originalName, PATHINFO_FILENAME);
            $storedFilename = $basename . '_part' . $filePart . '_' . time();
            if ($extension) {
                $storedFilename .= '.' . $extension;
            }

            $relativePath = $uploadedFile->storeAs('deudores/uploads', $storedFilename);

            $uploadId = DB::table('upload_progress')->insertGetId([
                'file_name' => $originalName,
                'file_part' => $filePart,
                'file_path' => $relativePath,
                'status' => 'cargado',
                'file_finish' => $request->input('file_finish'),
                'notify_email' => $request->input('notify_email'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Si es el iltimo archivo arrancamos el job
            if ($request->input('file_finish')) {
                ProcessDeudoresBCRAFiles::dispatch($request->input('file_name'));
            }

            return response()->json([
                'message' => 'Archivo subido y registrado con éxito',
                'upload_id' => $uploadId,
                'file_name' => $originalName,
                'file_part' => $filePart,
                'file_path' => $relativePath,
                'status' => 'cargado'
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error subiendo/registrando el archivo',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
