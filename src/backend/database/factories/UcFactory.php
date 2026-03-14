<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Uc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Uc>
 */
class UcFactory extends Factory
{
    protected $model = Uc::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'     => $this->faker->words(4, true),
            'sigla'    => strtoupper($this->faker->lexify('????')),
            'ects'     => $this->faker->numberBetween(3, 6),
            'horas'    => $this->faker->numberBetween(30, 90),
            'id_curso' => Curso::factory(),
        ];
    }
}
