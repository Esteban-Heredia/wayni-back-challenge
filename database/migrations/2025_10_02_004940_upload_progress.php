<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_progress', function (Blueprint $table) {
            $table->id();
            $table->string('notify_email');
            $table->string('file_name');
            $table->string('file_part');
            $table->string('file_path');
            $table->enum('status', ['cargado', 'procesando', 'listo en DB'])->default('cargado');
            $table->boolean('file_finish')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_progress');
    }
};
