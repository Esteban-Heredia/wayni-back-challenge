<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deudores', function (Blueprint $table) {
            $table->id();
            $table->string('nro_identificacion', 20)->index();
            $table->string('name_cliente', 255);
            $table->string('code_quien_debe', 5);
            $table->string('name_quien_debe', 255);
            $table->string('situacion_maxima', 2);
            $table->decimal('suma_total_prestamos', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deudores');
    }
};
