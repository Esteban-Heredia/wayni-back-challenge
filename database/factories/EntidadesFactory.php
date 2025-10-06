<?php

namespace Database\Factories;

use App\Models\entidades;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\entidades>
 */
class EntidadesFactory extends Factory
{
    protected $model = entidades::class;

    public function definition(): array
    {
        return [
            'codigo_entidad' => $this->faker->numerify('#####'),
            'name_entidad' => $this->faker->company(),
            'suma_total_prestamos' => number_format($this->faker->numberBetween(1000000, 2000000), 2, '.', ''),
        ];
    }
}
