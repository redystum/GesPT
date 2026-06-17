<?php

namespace App\Policies;

use App\Models\LoginDominios;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LoginDominiosPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('login_dominios.view_any');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('login_dominios.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LoginDominios $loginDominios): bool
    {
        return $user->hasPermission('login_dominios.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LoginDominios $loginDominios): bool
    {
        return $user->hasPermission('login_dominios.delete');
    }
}
