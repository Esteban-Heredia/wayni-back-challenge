<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\deudores;

class DeudoresCuilTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_deudor_existente_por_cuil(): void
    {
        deudores::factory()->create([
            'nro_identificacion' => '20307201225',
            'name_cliente' => 'MARTINEZ FACUNDO EZEQUIEL',
            'code_quien_debe' => '00007',
            'name_quien_debe' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
            'situacion_maxima' => '1',
            'suma_total_prestamos' => "292.00",
        ]);

        $response = $this->get('/api/deudores/20307201225');

        $response->assertStatus(200)
            ->assertJson([
                'nro_identificacion' => '20307201225',
                'name_cliente' => 'MARTINEZ FACUNDO EZEQUIEL',
                'code_quien_debe' => '00007',
                'name_quien_debe' => 'BANCO DE GALICIA Y BUENOS AIRES S.A.U.',
                'situacion_maxima' => '1',
                'suma_total_prestamos' => "292.00",
            ]);
    }

    public function test_devuelve_404_si_deudor_no_existe(): void
    {
        $response = $this->get('/api/deudores/00000000000');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Deudor no encontrado',
            ]);
    }
}
