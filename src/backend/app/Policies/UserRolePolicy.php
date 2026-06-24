<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Access\Response;

class UserRolePolicy
{
    /**
     * Determine whether the user can add any models.
     */
    public function add(User $user, UserRole $userRole): bool
    {
        return $user->hasPermission('user_roles.add') && $userRole->id_user !== $user->id;
    }

    /**
     * Determine whether the user can remove the model.
     */
    public function remove(User $user, UserRole $userRole): bool
    {
        return $user->hasPermission('user_roles.remove') && $userRole->id_user !== $user->id;
    }
}
