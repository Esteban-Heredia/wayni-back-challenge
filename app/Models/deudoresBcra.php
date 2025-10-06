<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class deudoresBcra extends Model
{
    protected $table = 'deudores_bcra';

    protected $fillable = [
        'codigo_entidad',
        'deuda',
    ];

    public function entidadCode()
    {
        return $this->belongsTo(entidadesCode::class, 'codigo_entidad', 'codigo_entidad');
    }
}
