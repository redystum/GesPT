<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Resources\Admin\RolesResource;
use App\Http\Resources\Admin\RolesShowResource;
use App\Models\Permissoes;
use App\Models\Role;
use App\Models\RolePermissoes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RolesController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . Role::class, only: ['index', 'all']),
            new Middleware('can:view,role', only: ['show']),
            new Middleware('can:create,' . Role::class, only: ['store']),
            new Middleware('can:update,role', only: ['update']),
            new Middleware('can:delete,role', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with(['permissoes', 'users'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%");
            });

        $roles = $query->paginate(50);
        return response()->json([
            'data' => RolesResource::collection($roles),
            'links' => [
                'first' => $roles->url(1),
                'last' => $roles->url($roles->lastPage()),
                'prev' => $roles->previousPageUrl(),
                'next' => $roles->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $roles->currentPage(),
                'from' => $roles->firstItem(),
                'last_page' => $roles->lastPage(),
                'links' => $roles->getUrlRange(1, $roles->lastPage()),
                'per_page' => $roles->perPage(),
                'to' => $roles->lastItem(),
                'total' => $roles->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $request->validated();

        return DB::transaction(function () use ($request) {
            $role = Role::create($request->only(['nome', 'descricao']));

            $ids_to_add = [];

            $this->storeRolePermissions($request, $ids_to_add, $role);

            return response()->json(['data' => new RolesResource($role)], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissoes']);
        return response()->json([
            'data' => RolesShowResource::make($role),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRoleRequest $request, Role $role)
    {
        $request->validated();

        if ($role->id === 1) {
            return response()->json([
                'message' => __('messages.role_admin_edit_forbidden'),
            ], 403);
        }

        return DB::transaction(function () use ($request, $role) {
            $role->update($request->only(['nome', 'descricao']));

            $ids_to_add = [];

            RolePermissoes::where('id_role', $role->id)->delete();
            $this->storeRolePermissions($request, $ids_to_add, $role);

            return response()->json(['data' => new RolesResource($role)], 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if ($role->id === 1) {
            return response()->json([
                'message' => __('messages.role_admin_delete_forbidden'),
            ], 403);
        }
        $role->delete();

        return response()->json([
            'message' => __('messages.role_deleted_success'),
        ], 200);
    }

    public function all()
    {
        return response()->json([
            'data' => RolesResource::collection(Role::all()),
        ]);
    }

    /**
     * @param StoreRoleRequest $request
     * @param array $ids_to_add
     * @param Role $role
     * @return void
     */
    private function storeRolePermissions(StoreRoleRequest $request, array $ids_to_add, Role $role): void
    {
        foreach ($request->permissoes as $permissao) {
            $perm = Permissoes::find($permissao);
            if ($perm && $perm->required_permissoes) {
                foreach ($perm->required_permissoes as $required) {
                    $req_perm = Permissoes::where('permissao', $required)->first()->id;
                    $ids_to_add[] = $req_perm;
                }
            }

            $ids_to_add[] = $permissao;
        }

        $ids_to_add = array_unique($ids_to_add);

        foreach ($ids_to_add as $id) {
            RolePermissoes::createOrFirst([
                'id_role' => $role->id,
                'id_permissao' => $id,
            ], [
                'id_role' => $role->id,
                'id_permissao' => $id,
            ]);
        }
    }
}
