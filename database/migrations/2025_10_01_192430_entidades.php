<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_entidad', 5);
            $table->string('name_entidad', 255);
            $table->decimal('suma_total_prestamos', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('codigo_entidad')
                  ->references('codigo_entidad')
                  ->on('entidades_code')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
