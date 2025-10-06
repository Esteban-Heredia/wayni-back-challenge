<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\deudores;

class GenerarDeudoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_genera_los_deudores_correctamente_desde_tablas_fuente(): void
    {
        DB::table('clientes')->insert([
            'cuit' => '20307201225',
            'name_cliente' => 'MARTINEZ FACUNDO EZEQUIEL',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('entidades_code')->insert([
            'codigo_entidad' => '00007',
            'name_entidad' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('deudores_bcra')->insert([
            'codigo_entidad' => '00007',
            'fecha_informacion' => '202501',
            'tipo_identificacion' => 'CU',
            'nro_identificacion' => '20307201225',
            'actividad' => 'A01',
            'situacion' => 1,
            'prestamos_total' => 292.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertCount(0, deudores::all());

        $response = $this->get('/api/deudoresFull');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tabla deudores generada con éxito',
                'total_registros' => 1,
            ]);

        $this->assertDatabaseHas('deudores', [
            'nro_identificacion' => '20307201225',
            'name_cliente' => 'MARTINEZ FACUNDO EZEQUIEL',
            'code_quien_debe' => '00007',
            'name_quien_debe' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
            'situacion_maxima' => 1,
            'suma_total_prestamos' => 292.00,
        ]);
    }
}
