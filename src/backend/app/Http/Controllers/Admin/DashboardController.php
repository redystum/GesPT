<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\PlanoEstudo;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;

class DashboardController extends Controller
{
    public function index()
    {
        $adminRole = Role::query()->where('id', 1)->first();

        $usersByRole = Role::query()
            ->withCount('users')
            ->orderByDesc('users_count')
            ->orderBy('nome')
            ->get()
            ->map(fn (Role $role) => [
                'label' => $role->nome,
                'value' => $role->users_count,
            ])
            ->values();

        return response()->json([
            'data' => [
                'stats' => [
                    'courses' => Curso::count(),
                    'study_plans' => PlanoEstudo::count(),
                    'users' => User::count(),
                    'admins' => $adminRole
                        ? UserRole::query()->where('id_role', $adminRole->id)->distinct('id_user')->count('id_user')
                        : 0,
                ],
                'charts' => [
                    'users_by_role' => $usersByRole,
                ],
            ],
        ]);
    }
}
