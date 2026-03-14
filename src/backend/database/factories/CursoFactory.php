<?php

namespace Database\Factories;

use App\Models\Curso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curso>
 */
class CursoFactory extends Factory
{
    protected $model = Curso::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'             => $this->faker->words(3, true),
            'sigla'            => strtoupper($this->faker->lexify('???')),
            'code'             => strtoupper($this->faker->bothify('??-###')),
            'id_curso_parente' => null,
        ];
    }
}
