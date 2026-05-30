<?php

namespace App\Policies;

use App\Models\PlanoEstudo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanoEstudoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('planos_estudo.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlanoEstudo $planoEstudo): bool
    {
        return $user->hasPermission('planos_estudo.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('planos_estudo.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlanoEstudo $planoEstudo): bool
    {
        return $user->hasPermission('planos_estudo.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlanoEstudo $planoEstudo): bool
    {
        return $user->hasPermission('planos_estudo.delete');
    }
}
