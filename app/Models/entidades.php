<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class entidades extends Model
{
    use HasFactory;

    protected $table = 'entidades';

    protected $fillable = [
        'codigo_entidad',
        'name_entidad',
        'suma_total_prestamos',
    ];

    public function deudores()
    {
        return $this->hasMany(deudoresBcra::class, 'codigo_entidad', 'codigo_entidad');
    }
}
