<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entidades_code', function (Blueprint $table) {
            $table->string('codigo_entidad', 5)->primary();
            $table->string('name_entidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entidades_code');
    }
};
