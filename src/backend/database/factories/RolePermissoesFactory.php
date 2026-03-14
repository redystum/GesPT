<?php

namespace Database\Factories;

use App\Models\Permissoes;
use App\Models\Role;
use App\Models\RolePermissoes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RolePermissoes>
 */
class RolePermissoesFactory extends Factory
{
    protected $model = RolePermissoes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_role'      => Role::factory(),
            'id_permissao' => Permissoes::factory(),
        ];
    }
}
