<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Deudores;

class DeudoresTopTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_los_top_deudores_ordenados_por_monto_descendente(): void
    {
        Deudores::factory()->create([
            'nro_identificacion' => '111',
            'name_cliente' => 'Cliente A',
            'suma_total_prestamos' => 500,
        ]);

        Deudores::factory()->create([
            'nro_identificacion' => '222',
            'name_cliente' => 'Cliente B',
            'suma_total_prestamos' => 1500,
        ]);

        Deudores::factory()->create([
            'nro_identificacion' => '333',
            'name_cliente' => 'Cliente C',
            'suma_total_prestamos' => 1000,
        ]);

        $response = $this->get('/api/deudores/top/2');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.nro_identificacion', '222')
            ->assertJsonPath('1.nro_identificacion', '333');
    }

    public function test_devuelve_error_si_cantidad_no_es_valida(): void
    {
        $response = $this->get('/api/deudores/top/abc');

        $response->assertStatus(400)
            ->assertJsonStructure([
                'error',
                'cantidad_ingresada'
            ]);
    }

    public function test_devuelve_vacio_si_no_hay_deudores(): void
    {
        $response = $this->get('/api/deudores/top/5');

        $response->assertStatus(200)
            ->assertExactJson([]);
    }
}
