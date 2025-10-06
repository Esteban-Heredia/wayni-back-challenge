<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UploadClientesTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_subir_archivo_de_clientes_y_guardar_registros(): void
    {
        // rear carpeta temporal que simule storage/app/private/clientes/uploads
        $uploadDir = storage_path('app/private/clientes/uploads');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Crear archivo físico temporal
        $contenido = "20304050607Cliente Uno\n10987654321Cliente Dos\n";
        $tempFilePath = $uploadDir . '/clientes_test.txt';
        file_put_contents($tempFilePath, $contenido);

        $fakeFile = new UploadedFile(
            $tempFilePath,
            'clientes_test.txt',
            'text/plain',
            null,
            true
        );

        $response = $this->postJson('/api/uploadClientes', [
            'file' => $fakeFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Archivo de clientes procesado e insertado con éxito',
            ]);

        $this->assertDatabaseHas('clientes', [
            'cuit' => '20304050607',
            'name_cliente' => 'Cliente Uno',
        ]);

        $this->assertDatabaseHas('clientes', [
            'cuit' => '10987654321',
            'name_cliente' => 'Cliente Dos',
        ]);

        if (file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }
    }

    public function test_valida_archivo_requerido(): void
    {
        $response = $this->postJson('/api/uploadClientes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }
}
