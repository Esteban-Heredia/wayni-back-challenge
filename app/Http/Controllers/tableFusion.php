<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\deudores;
use App\Models\entidadesCode;
use App\Models\entidades;
use Illuminate\Support\Facades\DB;

class tableFusion extends Controller
{
    public function EntidadesFull()
    {
        // limpiar la tabla de entidades por si se cambio los datos
        entidades::truncate();

        // se trae los datos de la tabla y se hace la fusion
        $resultados = entidadesCode::leftJoin('deudores_bcra', 'entidades_code.codigo_entidad', '=', 'deudores_bcra.codigo_entidad')
            ->select(
                'entidades_code.codigo_entidad',
                'entidades_code.name_entidad',
                DB::raw('COALESCE(SUM(deudores_bcra.prestamos_total),0) as suma_total_prestamos')
            )
            ->groupBy('entidades_code.codigo_entidad', 'entidades_code.name_entidad')
            ->get();

        foreach ($resultados as $row) {
            entidades::create([
                'codigo_entidad'        => $row->codigo_entidad,
                'name_entidad'          => $row->name_entidad,
                'suma_total_prestamos'  => $row->suma_total_prestamos,
            ]);
        }

        return response()->json([
            'message' => 'Fusión completada con éxito',
            'total_registros' => count($resultados),
        ]);
    }

    public function generarDeudores()
    {
        deudores::truncate();

        $resultados = DB::table('deudores_bcra')
            ->join('clientes', 'deudores_bcra.nro_identificacion', '=', 'clientes.cuit')
            ->join('entidades_code', 'deudores_bcra.codigo_entidad', '=', 'entidades_code.codigo_entidad')
            ->select(
                'deudores_bcra.nro_identificacion',
                'clientes.name_cliente',
                'deudores_bcra.codigo_entidad as code_quien_debe',
                'entidades_code.name_entidad as name_quien_debe',
                'deudores_bcra.situacion as situacion_maxima',
                DB::raw('COALESCE(deudores_bcra.prestamos_total,0) as suma_total_prestamos')
            )
            ->get();

        foreach ($resultados as $row) {
            deudores::create([
                'nro_identificacion'    => $row->nro_identificacion,
                'name_cliente'          => $row->name_cliente,
                'code_quien_debe'       => $row->code_quien_debe,
                'name_quien_debe'       => $row->name_quien_debe,
                'situacion_maxima'      => $row->situacion_maxima,
                'suma_total_prestamos'  => $row->suma_total_prestamos,
            ]);
        }

        return response()->json([
            'message' => 'Tabla deudores generada con éxito',
            'total_registros' => count($resultados),
        ]);
    }
}
