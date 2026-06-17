<?php

namespace App\Policies;

use App\Models\Uc;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UcPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ucs.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Uc $uc): bool
    {
        return $user->hasPermission('ucs.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('ucs.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Uc $uc): bool
    {
        return $user->hasPermission('ucs.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Uc $uc): bool
    {
        return $user->hasPermission('ucs.delete');
    }
}
