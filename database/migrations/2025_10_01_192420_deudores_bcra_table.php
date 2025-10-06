<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deudores_bcra', function (Blueprint $table) {
            $table->string('codigo_entidad', 5)->index();
            $table->string('fecha_informacion', 6);
            $table->string('tipo_identificacion', 2);
            $table->string('nro_identificacion', 20)->index();
            $table->string('actividad', 3);
            $table->unsignedTinyInteger('situacion');

            $table->decimal('prestamos_total', 15, 2)->nullable();
            $table->decimal('sin_uso', 15, 2)->nullable();
            $table->decimal('garantias_otorgadas', 15, 2)->nullable();
            $table->decimal('otros_conceptos', 15, 2)->nullable();
            $table->decimal('garantias_preferidas_a', 15, 2)->nullable();
            $table->decimal('garantias_preferidas_b', 15, 2)->nullable();
            $table->decimal('sin_garantias_preferidas', 15, 2)->nullable();
            $table->decimal('contragarantias_preferidas_a', 15, 2)->nullable();
            $table->decimal('contragarantias_preferidas_b', 15, 2)->nullable();
            $table->decimal('sin_contragarantias_preferidas', 15, 2)->nullable();
            $table->decimal('previsiones', 15, 2)->nullable();

            $table->tinyInteger('deuda_cubierta')->nullable();
            $table->tinyInteger('proceso_judicial')->nullable();
            $table->tinyInteger('refinanciaciones')->nullable();
            $table->tinyInteger('recategorizacion_obligatoria')->nullable();
            $table->tinyInteger('situacion_juridica')->nullable();
            $table->tinyInteger('irrecuperables')->nullable();

            $table->integer('dias_atraso')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deudores_bcra');
    }
};
