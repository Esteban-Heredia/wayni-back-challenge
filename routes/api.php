<?php

use App\Http\Controllers\queries;
use App\Http\Controllers\tableFusion;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\uploadTxt;

// Rutas de subida de archivos

Route::post('/uploadEntidades', [uploadTxt::class, 'uploadEntidades']);

Route::post('/uploadClientes', [uploadTxt::class, 'uploadClientes']);

Route::post('/uploadFileDeudoresBCRA', [uploadTxt::class, 'uploadFileDeudoresBCRA'])->withoutMiddleware('auth:sanctum');


// Rutas de fusion de tablas

Route::get('/entidadesFull', [tableFusion::class, 'EntidadesFull']);

Route::get('/deudoresFull', [tableFusion::class, 'generarDeudores']);



// Rutas de consultas

Route::get('/deudores/{nro_identificacion}', [queries::class, 'showCuil']);

Route::get('/entidades/{codigo}', [queries::class, 'showEntidad']);

Route::get('/deudores/top/{cantidadDeDeudores}', [queries::class, 'showDeudoresTop']);

Route::get('/deudores/situaciones/{codigo}', [queries::class, 'showDeudoresPorSituacion']);