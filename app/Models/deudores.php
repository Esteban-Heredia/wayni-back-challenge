<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deudores extends Model
{
    use HasFactory;
    
    protected $table = 'deudores';

    protected $fillable = [
        'nro_identificacion',
        'name_cliente',
        'code_quien_debe',
        'name_quien_debe',
        'situacion_maxima',
        'suma_total_prestamos',
    ];
}
