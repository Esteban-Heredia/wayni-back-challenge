<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\entidades;
use App\Models\deudores;

class queries extends Controller
{
    public function showEntidad($codigo)
    {
        $entidad = entidades::where('codigo_entidad', $codigo)->first();

        if (!$entidad) {
            return response()->json([
                'message' => 'Entidad no encontrada'
            ], 404);
        }

        return response()->json($entidad);
    }

    public function showCuil($nro_identificacion)
    {
        $deudor = deudores::where('nro_identificacion', $nro_identificacion)->first();

        if (!$deudor) {
            return response()->json(['message' => 'Deudor no encontrado'], 404);
        }

        return response()->json($deudor);
    }

    public function showDeudoresTop($cantidadDeDeudores = null)
    {
        if ($cantidadDeDeudores !== null && (!is_numeric($cantidadDeDeudores) || (int)$cantidadDeDeudores <= 0)) {
            return response()->json([
                'error' => 'Cantidad inválida',
                'cantidad_ingresada' => $cantidadDeDeudores
            ], 400);
        }

        $query = deudores::orderBy('suma_total_prestamos', 'desc');

        if ($cantidadDeDeudores) {
            $query->take($cantidadDeDeudores);
        }

        $topDeudores = $query->get();

        return response()->json($topDeudores);
    }

public function showDeudoresPorSituacion(Request $request, $codigo)
{
    // elimina ceros a la izquierda
    $codigo = (string)(int)$codigo;

    $mapSituaciones = [
        '1'  => 'Situación Normal',
        '21' => 'Riesgo Bajo',
        '23' => 'En tratamiento especial (a partir de abril 2020)',
        '3'  => 'Riesgo Medio',
        '4'  => 'Riesgo Alto',
        '5'  => 'Irrecuperable',
        '11' => 'Con asistencias cubiertas en su totalidad con garantías preferidas “A”',
    ];

    if (!array_key_exists($codigo, $mapSituaciones)) {
        return response()->json([
            'error' => 'Código de situación inválido',
            'codigo_ingresado' => $codigo,
            'codigos_validos' => array_keys($mapSituaciones)
        ], 400);
    }

    // paginacion
    $limit = $request->get('limit', 20);
    $limit = min(max((int)$limit, 1), 100);

    // aplica paginación
    $paginator = deudores::where('situacion_maxima', $codigo)
        ->orderBy('suma_total_prestamos', 'desc')
        ->paginate($limit, [
            'nro_identificacion',
            'name_cliente',
            'code_quien_debe',
            'name_quien_debe',
            'situacion_maxima',
            'suma_total_prestamos'
        ]);

    // transforma los datos del paginator conservando la estructura
    $paginator->getCollection()->transform(function ($row) use ($mapSituaciones) {
        return [
            'nro_identificacion'   => $row->nro_identificacion,
            'name_cliente'         => $row->name_cliente,
            'code_quien_debe'      => $row->code_quien_debe,
            'name_quien_debe'      => $row->name_quien_debe,
            'situacion_maxima'     => $row->situacion_maxima,
            'descripcion'          => $mapSituaciones[$row->situacion_maxima] ?? 'Desconocido',
            'suma_total_prestamos' => $row->suma_total_prestamos,
        ];
    });

    return response()->json([
        'data' => $paginator->items(),
        'meta' => [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ]
    ]);
}

}
