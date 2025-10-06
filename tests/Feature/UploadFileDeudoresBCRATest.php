<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\ProcessDeudoresBCRAFiles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class UploadFileDeudoresBCRATest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_subir_archivo_y_guardar_registro(): void
    {
        Storage::fake('local');
        Queue::fake();

        // Archivo falso
        $fakeFile = UploadedFile::fake()->create('deudores_test.txt', 10, 'text/plain');

        $response = $this->postJson('/api/uploadFileDeudoresBCRA', [
            'file' => $fakeFile,
            'file_part' => 1,
            'file_name' => 'deudores_test.txt',
            'file_finish' => true,
            'notify_email' => 'test@correo.com',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Archivo subido y registrado con éxito',
                     'file_name' => 'deudores_test.txt',
                     'file_part' => 1,
                     'status' => 'cargado',
                 ]);

        $this->assertDatabaseHas('upload_progress', [
            'file_name' => 'deudores_test.txt',
            'file_part' => 1,
            'status' => 'cargado',
            'notify_email' => 'test@correo.com',
        ]);

        // Verifica que se haya despachado el Job
        Queue::assertPushed(ProcessDeudoresBCRAFiles::class, function ($job) {
            return $job->fileName === 'deudores_test.txt';
        });
    }

    public function valida_campos_requeridos(): void
    {
        $response = $this->postJson('/api/uploadFileDeudoresBCRA', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'file', 'file_part', 'file_name', 'file_finish', 'notify_email'
                 ]);
    }
}
