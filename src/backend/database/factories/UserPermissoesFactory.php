<?php

namespace Database\Factories;

use App\Models\Permissoes;
use App\Models\User;
use App\Models\UserPermissoes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPermissoes>
 */
class UserPermissoesFactory extends Factory
{
    protected $model = UserPermissoes::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user'      => User::factory(),
            'id_permiscao' => Permissoes::factory(),
        ];
    }
}
