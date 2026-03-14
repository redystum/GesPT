<?php

namespace Database\Factories;

use App\Models\Estudante;
use App\Models\PlanoEstudo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\app\Models\Estudante>
 */
class EstudanteFactory extends Factory
{
    protected $model = Estudante::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email'          => $this->faker->unique()->safeEmail(),
            'plano_de_estudo' => PlanoEstudo::factory(),
        ];
    }
}
