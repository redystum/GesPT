<?php

namespace Database\Factories;

use App\Models\PlanoCadeira;
use App\Models\PlanoEstudo;
use App\Models\Uc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\app\Models\PlanoCadeira>
 */
class PlanoCadeiraFactory extends Factory
{
    protected $model = PlanoCadeira::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_plano' => PlanoEstudo::factory(),
            'id_uc'    => Uc::factory(),
            'opcional' => $this->faker->boolean(20),
        ];
    }
}
