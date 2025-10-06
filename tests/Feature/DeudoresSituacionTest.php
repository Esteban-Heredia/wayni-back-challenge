<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Deudores;

class DeudoresSituacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_deudores_por_situacion_valida_ordenados_por_deuda(): void
    {
        Deudores::factory()->create([
            'nro_identificacion' => '111',
            'name_cliente' => 'Cliente A',
            'situacion_maxima' => '5',
            'suma_total_prestamos' => 500,
        ]);

        Deudores::factory()->create([
            'nro_identificacion' => '222',
            'name_cliente' => 'Cliente B',
            'situacion_maxima' => '5',
            'suma_total_prestamos' => 1000,
        ]);

        $response = $this->get('/api/deudores/situaciones/5');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['nro_identificacion' => '222'])
            ->assertJsonFragment(['nro_identificacion' => '111']);
    }

    public function test_devuelve_error_si_codigo_situacion_invalido(): void
    {
        $response = $this->get('/api/deudores/situaciones/99');

        $response->assertStatus(400)
            ->assertJsonStructure([
                'error',
                'codigo_ingresado',
                'codigos_validos'
            ]);
    }
}
