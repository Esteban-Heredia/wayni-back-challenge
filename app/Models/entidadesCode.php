<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class entidadesCode extends Model
{
    protected $table = 'entidades_code';

    protected $fillable = [
        'codigo_entidad',
        'name_entidad',
    ];

    public function deudores()
    {
        return $this->hasMany(deudoresBcra::class, 'codigo_entidad', 'codigo_entidad');
    }
}
