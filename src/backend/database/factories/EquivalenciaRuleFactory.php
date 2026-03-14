<?php

namespace Database\Factories;

use App\Models\EquivalenciaRule;
use App\Models\PlanoEstudo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EquivalenciaRule>
 */
class EquivalenciaRuleFactory extends Factory
{
    protected $model = EquivalenciaRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_plano' => PlanoEstudo::factory(),
        ];
    }
}
