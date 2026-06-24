<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\UserShowResource;
use App\Models\User;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => UserShowResource::make($user->load('roles', 'permissoes')),
            'permissions' => $user->allPermissoes(),
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'sometimes|string|in:light,dark',
            'lang' => 'sometimes|string|in:pt,en',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'message' => 'Preferences updated successfully.',
            'user' => UserShowResource::make($user->load('roles', 'permissoes')),
        ]);
    }
}
