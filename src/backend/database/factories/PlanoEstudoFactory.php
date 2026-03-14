<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\PlanoEstudo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\app\Models\PlanoEstudo>
 */
class PlanoEstudoFactory extends Factory
{
    protected $model = PlanoEstudo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ano'      => $this->faker->numberBetween(2020, 2026),
            'id_curso' => Curso::factory(),
        ];
    }
}
