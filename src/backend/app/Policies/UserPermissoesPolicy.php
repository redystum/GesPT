<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPermissoes;
use App\Models\UserRole;
use Illuminate\Auth\Access\Response;

class UserPermissoesPolicy
{
    /**
     * Determine whether the user can add any models.
     */
    public function add(User $user, UserPermissoes $userPermissions): bool
    {
        return $user->hasPermission('user_permissoes.add') && $userPermissions->id_user !== $user->id;
    }

    /**
     * Determine whether the user can remove the model.
     */
    public function remove(User $user, UserPermissoes $userPermissions): bool
    {
        return $user->hasPermission('user_permissoes.remove') && $userPermissions->id_user !== $user->id;
    }
}
