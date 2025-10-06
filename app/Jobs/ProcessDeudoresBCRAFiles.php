<?php

namespace App\Jobs;

use App\Services\EmailServices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDeudoresBCRAFiles implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $fileName;
    public $batchSize = 1000;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function handle()
    {
        ini_set('memory_limit', '6G');
        set_time_limit(0);

        $parts = DB::table('upload_progress')
            ->where('file_name', $this->fileName)
            ->orderBy('file_part', 'asc')
            ->get();

        if ($parts->isEmpty()) {
            Log::warning("si no encuentra el archivo {$this->fileName}");
            return;
        }

        $batch = [];

        foreach ($parts as $part) {
            // va maarcando como procesando
            DB::table('upload_progress')
                ->where('id', $part->id)
                ->update(['status' => 'procesando', 'updated_at' => now()]);

            $filePath = storage_path('app/private/' . $part->file_path);

            if (!file_exists($filePath)) {
                Log::error("donde esta???? {$filePath}");
                continue;
            }

            $file = new \SplFileObject($filePath);

            while (!$file->eof()) {
                $line = $file->fgets();
                $line = mb_convert_encoding($line, 'UTF-8', 'UTF-16, ISO-8859-1, ASCII');
                $line = preg_replace('/[\x00-\x1F\x7F]/u', '', $line);
                $line = trim($line);

                if ($line === '' || strlen($line) < 168) continue;

                $batch[] = [
                    'codigo_entidad'                => substr($line, 0, 5),
                    'fecha_informacion'             => substr($line, 5, 6),
                    'tipo_identificacion'           => substr($line, 11, 2),
                    'nro_identificacion'            => preg_replace('/\D/', '', substr($line, 13, 11)),
                    'actividad'                     => substr($line, 24, 3),
                    'situacion'                     => (int) substr($line, 27, 2),
                    'prestamos_total'               => (float) str_replace(',', '.', trim(substr($line, 29, 12))),
                    'sin_uso'                       => (float) str_replace(',', '.', trim(substr($line, 41, 12))),
                    'garantias_otorgadas'           => (float) str_replace(',', '.', trim(substr($line, 53, 12))),
                    'otros_conceptos'               => (float) str_replace(',', '.', trim(substr($line, 65, 12))),
                    'garantias_preferidas_a'        => (float) str_replace(',', '.', trim(substr($line, 77, 12))),
                    'garantias_preferidas_b'        => (float) str_replace(',', '.', trim(substr($line, 89, 12))),
                    'sin_garantias_preferidas'      => (float) str_replace(',', '.', trim(substr($line, 101, 12))),
                    'contragarantias_preferidas_a'  => (float) str_replace(',', '.', trim(substr($line, 113, 12))),
                    'contragarantias_preferidas_b'  => (float) str_replace(',', '.', trim(substr($line, 125, 12))),
                    'sin_contragarantias_preferidas' => (float) str_replace(',', '.', trim(substr($line, 137, 12))),
                    'previsiones'                   => (float) str_replace(',', '.', trim(substr($line, 149, 12))),
                    'deuda_cubierta'                => (int) substr($line, 161, 1),
                    'proceso_judicial'              => (int) substr($line, 162, 1),
                    'refinanciaciones'              => (int) substr($line, 163, 1),
                    'recategorizacion_obligatoria'  => (int) substr($line, 164, 1),
                    'situacion_juridica'            => (int) substr($line, 165, 1),
                    'irrecuperables'                => (int) substr($line, 166, 1),
                    'dias_atraso'                   => (int) substr($line, 167, 4),
                    'created_at'                    => now(),
                    'updated_at'                    => now(),
                ];

                if (count($batch) >= $this->batchSize) {
                    DB::table('deudores_bcra')->insert($batch);
                    $batch = [];
                }
            }

            // carga lo que queda
            if (!empty($batch)) {
                DB::table('deudores_bcra')->insert($batch);
                $batch = [];
            }

            DB::table('upload_progress')
                ->where('id', $part->id)
                ->update(['status' => 'listo en DB', 'updated_at' => now()]);

            if (file_exists($filePath)) {
                try {
                    unlink($filePath);
                    Log::info("Archivo borrado: {$filePath}");
                } catch (\Throwable $e) {
                    Log::error("No se pudo borrar {$filePath}: " . $e->getMessage());
                }
            }
        }

        $finalParts = DB::table('upload_progress')
            ->where('file_name', $this->fileName)
            ->where('status', 'listo en DB')
            ->get();

        foreach ($finalParts as $fp) {
            $finalPath = storage_path('app/private/' . $fp->file_path);
            if (file_exists($finalPath)) {
                try {
                    unlink($finalPath);
                    Log::info("Archivo residual eliminado: {$finalPath}");
                } catch (\Throwable $e) {
                    Log::error("No se pudo eliminar archivo residual {$finalPath}: " . $e->getMessage());
                }
            }
        }

        DB::table('upload_progress')
            ->where('file_name', $this->fileName)
            ->where('status', 'listo en DB')
            ->delete();

        Log::info("Limpieza final completada para {$this->fileName}");

        app(EmailServices::class)->sendJobFinished(
            $part->notify_email,
            "Se termino de procesar el archivo {$this->fileName}"
        );
    }
}
