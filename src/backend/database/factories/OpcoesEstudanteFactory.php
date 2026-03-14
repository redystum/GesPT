<?php

namespace Database\Factories;

use App\Models\Estudante;
use App\Models\Opcoes;
use App\Models\OpcoesEstudante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\app\Models\OpcoesEstudante>
 */
class OpcoesEstudanteFactory extends Factory
{
    protected $model = OpcoesEstudante::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_estudante' => Estudante::factory(),
            'id_opcao'     => Opcoes::factory(),
        ];
    }
}
