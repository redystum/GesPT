<?php

namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

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
        return response()->json([
            'data' => UserResource::make($user),
        ], 200);
    }

    public function store(StoreUserRequest $request){
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'User criado com sucesso!',
            'data' => UserResource::make($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user){
        $user->update($request->validated());

        return response()->json([
            'message' => 'User atualizado com sucesso!',
            'data' => UserResource::make($user),
        ], 200);
    }

    public function destroy(User $user){
        $user->delete();

        return response()->json([
            'message' => 'User eliminado com sucesso!',
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();
        dd($request->all());

        $user = User::where('email', $request->email)->first();

        if ($user && $user->type === User::USER_TYPE_LOCAL){
            if (auth()->attempt($request->only('email', 'password'), $request->remember)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Login successful',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ], 200);
            }
        }

        // TODO: request ipleiria

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
