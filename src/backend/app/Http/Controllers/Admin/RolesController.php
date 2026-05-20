<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Resources\Admin\RolesResource;
use App\Http\Resources\Admin\RolesShowResource;
use App\Models\Role;
use App\Models\RolePermissoes;
use Illuminate\Http\Request;

class RolesController extends Controller
{
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

        $role = Role::create($request->only(['nome', 'descricao']));

        foreach ($request->permissoes as $permissao) {
            RolePermissoes::create([
                'id_role' => $role->id,
                'id_permissao' => $permissao,
            ]);
        }

        return response()->json(['data' => new RolesResource($role)], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with(['permissoes'])->findOrFail($id);
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

        $role->update($request->only(['nome', 'descricao']));

        // Atualizar as permissões associadas
        RolePermissoes::where('id_role', $role->id)->delete();
        foreach ($request->permissoes as $permissao) {
            RolePermissoes::create([
                'id_role' => $role->id,
                'id_permissao' => $permissao,
            ]);
        }

        return response()->json(['data' => new RolesResource($role)], 200);
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
}
