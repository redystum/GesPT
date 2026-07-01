<?php

namespace App\Policies;

use App\Models\User;

class AuditLogPolicy
{
    /**
     * Determine whether the user can view any audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('audit_logs.view_any');
    }
}
