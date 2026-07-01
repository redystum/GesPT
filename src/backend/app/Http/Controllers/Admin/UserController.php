<?php

namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\UserShowResource;
use App\Models\User;
use App\Models\UserPermissoes;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . User::class, only: ['index']),
            new Middleware('can:view,user', only: ['show']),
            new Middleware('can:create,' . User::class, only: ['store']),
            new Middleware('can:update,user', only: ['update']),
            new Middleware('can:delete,user', only: ['destroy']),
            new Middleware('can:block,user', only: ['block', 'unblock']),
        ];
    }
    public function index(Request $request){
        $users = User::with(['roles', 'permissoes'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(50);
        return UserResource::collection($users);
    }

    public function show(User $user){
        $user->load(['roles.permissoes', 'permissoes']);
        return response()->json([
            'data' => UserShowResource::make($user),
        ], 200);
    }

    public function store(StoreUserRequest $request){
        $user = User::create($request->validated());

        return response()->json([
            'message' => __('messages.user_created_success'),
            'data' => UserResource::make($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user){
        if ($user->id == 1){
            if ($request->has('roles') || $request->has('custom_permissions')) {
                return response()->json([
                    'message' => __('messages.user_updated_admin_forbidden'),
                ], 403);
            }
        }

        if ($request->has('roles')) {
            $userRoleDummy = new UserRole(['id_user' => $user->id]);
            $this->authorize('add', $userRoleDummy);
            $this->authorize('remove', $userRoleDummy);
        }

        if ($request->has('custom_permissions')) {
            $userPermsDummy = new UserPermissoes(['id_user' => $user->id]);
            $this->authorize('add', $userPermsDummy);
            $this->authorize('remove', $userPermsDummy);
        }

        $oldRoles = $user->roles()->pluck('nome')->toArray();
        $oldPermissions = $user->permissoes()->pluck('nome')->toArray();

        DB::beginTransaction();

        try {
            $user->update($request->only('name', 'recovery_email'));

            if ($request->has('roles')) {
                UserRole::where('id_user', $user->id)->delete();
                foreach ($request->roles ?? [] as $role){
                    UserRole::create([
                        'id_user' => $user->id,
                        'id_role' => $role,
                    ]);
                }
            }

            if ($request->has('custom_permissions')) {
                UserPermissoes::where('id_user', $user->id)->delete();
                foreach ($request->custom_permissions ?? [] as $permission){
                    UserPermissoes::create([
                        'id_user' => $user->id,
                        'id_permissao' => $permission,
                    ]);
                }
            }

            $newRoles = $user->roles()->pluck('nome')->toArray();
            $newPermissions = $user->permissoes()->pluck('nome')->toArray();

            $addedRoles = array_diff($newRoles, $oldRoles);
            $removedRoles = array_diff($oldRoles, $newRoles);
            $addedPermissions = array_diff($newPermissions, $oldPermissions);
            $removedPermissions = array_diff($oldPermissions, $newPermissions);

            $causer = auth()->user();

            foreach ($addedRoles as $role) {
                activity()
                    ->causedBy($causer)
                    ->performedOn($user)
                    ->useLog('user_roles')
                    ->withProperties([
                        'target_name' => $user->name,
                        'role_name' => $role,
                        'action' => 'added'
                    ])
                    ->log("Role $role added to {$user->name}");
            }

            foreach ($removedRoles as $role) {
                activity()
                    ->causedBy($causer)
                    ->performedOn($user)
                    ->useLog('user_roles')
                    ->withProperties([
                        'target_name' => $user->name,
                        'role_name' => $role,
                        'action' => 'removed'
                    ])
                    ->log("Role $role removed from {$user->name}");
            }

            if (count($addedPermissions) > 0) {
                activity()
                    ->causedBy($causer)
                    ->performedOn($user)
                    ->useLog('user_permissions')
                    ->withProperties([
                        'target_name' => $user->name,
                        'permissions' => array_values($addedPermissions),
                        'action' => 'added'
                    ])
                    ->log(count($addedPermissions) . " permissions added to {$user->name}");
            }

            if (count($removedPermissions) > 0) {
                activity()
                    ->causedBy($causer)
                    ->performedOn($user)
                    ->useLog('user_permissions')
                    ->withProperties([
                        'target_name' => $user->name,
                        'permissions' => array_values($removedPermissions),
                        'action' => 'removed'
                    ])
                    ->log(count($removedPermissions) . " permissions removed from {$user->name}");
            }

            DB::commit();
        } catch (Throwable $error) {
            DB::rollBack();

            return response()->json([
                'message' => __('messages.user_updated_error'),
                'error' => $error->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('messages.user_updated_success'),
            'data' => UserResource::make($user),
        ], 200);
    }

    public function destroy(User $user){
        if ($user->id == 1){
            return response()->json([
                'message' => __('messages.user_admin_deleted_forbidden'),
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => __('messages.user_deleted_success'),
        ], 200);
    }

    public function block(User $user, Request $request)
    {
        if ($user->id == 1){
            return response()->json([
                'message' => __('messages.user_admin_blocked_forbidden'),
            ], 403);
        }

        $user->update([
            'blocked' => true,
            'blocked_reason' => $request->input('reason'),
            'blocked_at' => now(),
        ]);

        return response()->json([
            'message' => __('messages.user_blocked_success'),
        ], 200);
    }

    public function unblock(User $user)
    {
        if ($user->id == 1){
            return response()->json([
                'message' => __('messages.user_admin_unblocked_forbidden'),
            ], 403);
        }

        $user->update([
            'blocked' => false,
            'blocked_reason' => null,
            'blocked_at' => null,
        ]);

        return response()->json([
            'message' => __('messages.user_unblocked_success'),
        ], 200);
    }
}
