<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\entidades;

class EntidadesFullTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_entidades_correctamente_desde_tablas_fuente(): void
    {
        DB::table('entidades_code')->insert([
            [
                'codigo_entidad' => '00001',
                'name_entidad' => 'BANCO DE LA NACION ARGENTINA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_entidad' => '00002',
                'name_entidad' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('deudores_bcra')->insert([
            [
                'codigo_entidad' => '00001',
                'fecha_informacion' => '202501',
                'tipo_identificacion' => 'CU',
                'nro_identificacion' => '20307201225',
                'actividad' => 'A01',
                'situacion' => 1,
                'prestamos_total' => 1000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_entidad' => '00002',
                'fecha_informacion' => '202501',
                'tipo_identificacion' => 'CU',
                'nro_identificacion' => '20307201226',
                'actividad' => 'A01',
                'situacion' => 1,
                'prestamos_total' => 500.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->assertCount(0, entidades::all());

        $response = $this->get('/api/entidadesFull');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Fusión completada con éxito',
                'total_registros' => 2,
            ]);

        $this->assertDatabaseHas('entidades', [
            'codigo_entidad' => '00001',
            'name_entidad' => 'BANCO DE LA NACION ARGENTINA',
            'suma_total_prestamos' => 1000.00,
        ]);

        $this->assertDatabaseHas('entidades', [
            'codigo_entidad' => '00002',
            'name_entidad' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
            'suma_total_prestamos' => 500.00,
        ]);
    }
}
