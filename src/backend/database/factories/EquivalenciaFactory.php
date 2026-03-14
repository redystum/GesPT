<?php

namespace Database\Factories;

use App\Models\Equivalencia;
use App\Models\EquivalenciaRule;
use App\Models\Uc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equivalencia>
 */
class EquivalenciaFactory extends Factory
{
    protected $model = Equivalencia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasUc = $this->faker->boolean(75);

        return [
            'id_rule' => EquivalenciaRule::factory(),
            'id_uc'   => $hasUc ? Uc::factory() : null,
            'lado'    => $this->faker->randomElement(['Original', 'Final']),
            'custom'  => $hasUc ? null : $this->faker->sentence(),
        ];
    }
}
