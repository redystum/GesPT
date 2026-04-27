<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index(){
        return UserResource::collection(User::all());
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
}
