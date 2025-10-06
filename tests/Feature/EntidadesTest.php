<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\entidades;

class EntidadesTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_entidad_existente(): void
    {
        DB::table('entidades_code')->insert([
            'codigo_entidad' => '00011',
            'name_entidad' => 'BANCO DE LA NACION ARGENTINA'
        ]);

        entidades::factory()->create([
            'codigo_entidad' => '00011',
            'name_entidad' => 'BANCO DE LA NACION ARGENTINA',
            'suma_total_prestamos' => "1387108270.00",
        ]);

        $response = $this->get('/api/entidades/00011');

        $response->assertStatus(200)
            ->assertJson([
                'codigo_entidad' => '00011',
                'name_entidad' => 'BANCO DE LA NACION ARGENTINA',
                'suma_total_prestamos' => "1387108270.00",
            ]);
    }

    public function test_devuelve_404_si_entidad_no_existe(): void
    {
        $response = $this->get('/api/entidades/99999');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Entidad no encontrada'
            ]);
    }
}
