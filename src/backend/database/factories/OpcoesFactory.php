<?php

namespace Database\Factories;

use App\Models\Opcoes;
use App\Models\Uc;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opcoes>
 */
class OpcoesFactory extends Factory
{
    protected $model = Opcoes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_uc_opcional' => Uc::factory(),
            'id_uc'          => Uc::factory(),
        ];
    }
}
