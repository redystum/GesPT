<?php

namespace Database\Factories;

use App\Models\Permissoes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permissoes>
 */
class PermissoesFactory extends Factory
{
    protected $model = Permissoes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        $resources = ['cursos', 'ucs', 'estudantes', 'planos', 'equivalencias', 'roles', 'users'];

        $action   = $this->faker->randomElement($actions);
        $resource = $this->faker->randomElement($resources);

        return [
            'permissao' => "{$action}_{$resource}",
            'nome'      => ucfirst($action) . ' ' . ucfirst($resource),
            'descricao' => $this->faker->sentence(),
        ];
    }
}
