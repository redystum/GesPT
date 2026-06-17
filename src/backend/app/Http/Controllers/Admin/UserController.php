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

class UserController extends Controller
{
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
            return response()->json([
                'message' => __('messages.user_updated_admin_forbidden'),
            ], 403);
        }

        DB::beginTransaction();

        try {
            $user->update($request->only('name'));

            UserRole::where('id_user', $user->id)->delete();
            foreach ($request->roles ?? [] as $role){
                UserRole::create([
                    'id_user' => $user->id,
                    'id_role' => $role,
                ]);
            }

            UserPermissoes::where('id_user', $user->id)->delete();
            foreach ($request->custom_permissions ?? [] as $permission){
                UserPermissoes::create([
                    'id_user' => $user->id,
                    'id_permissao' => $permission,
                ]);
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
