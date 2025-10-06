<?php

namespace Database\Factories;

use App\Models\deudores;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\deudores>
 */
class deudoresFactory extends Factory
{

    use HasFactory;

    protected $model = deudores::class;

    public function definition(): array
    {
        return [
            'nro_identificacion'    => $this->faker->unique()->numberBetween(10000000000, 99999999999),
            'name_cliente'          => $this->faker->name(),
            'code_quien_debe'       => '00001',
            'name_quien_debe'       => 'Entidad de prueba',
            'situacion_maxima' => $this->faker->randomElement([1, 21, 23, 3, 4, 5, 11]),
            'suma_total_prestamos'  => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}
