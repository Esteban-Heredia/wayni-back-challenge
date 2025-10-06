<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadEntidadesTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_subir_archivo_de_entidades_y_guardar_registros(): void
    {
        // Crear carpeta temporal que simule storage/app/private/entidades/uploads
        $uploadDir = storage_path('app/private/entidades/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Crear archivo físico temporal
        $contenido = "12345Entidad Uno\n54321Entidad Dos\n";
        $tempFilePath = $uploadDir . '/entidades_test.txt';
        file_put_contents($tempFilePath, $contenido);

        $fakeFile = new UploadedFile(
            $tempFilePath,
            'entidades_test.txt',
            'text/plain',
            null,
            true
        );

        $response = $this->postJson('/api/uploadEntidades', [
            'file' => $fakeFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Archivo procesado y cargado en la DB',
            ]);

        $this->assertDatabaseHas('entidades_code', [
            'codigo_entidad' => '12345',
            'name_entidad' => 'Entidad Uno',
        ]);

        $this->assertDatabaseHas('entidades_code', [
            'codigo_entidad' => '54321',
            'name_entidad' => 'Entidad Dos',
        ]);

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }
    }

    public function test_valida_archivo_requerido(): void
    {
        $response = $this->postJson('/api/uploadEntidades', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }
}
