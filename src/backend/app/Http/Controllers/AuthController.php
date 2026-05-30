<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\Admin\UserShowResource;
use App\Models\LoginDominios;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * @param string $email
     * @param string $password
     * @return int `-1` no url <br> `-2` failed request <br> `0` wrong credentials <br> `1` correct credentials
     */
    private function requestIPLeiriaAuth(string $email, string $password)
    {
        $local_part = explode('@', $email)[0];
        $url = config('services.ipleiria.auth');

        if (!$url) {
            return -1;
        }

        try {
            $response = Http::asForm()->post($url, [
                'a' => $local_part,
                'b' => $password,
            ]);
        } catch (ConnectionException $_) {
            return -1;
        }

        if ($response->successful()) {
            return $response->body() === 'true' ? 1 : 0;
        } else {
            return -2;
        }
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        if (auth('sanctum')->check()) {
            return response()->json(['message' => 'You are already logged in.'], 403);
        }

        $domains = LoginDominios::all();
        $user_domain = explode('@', $request->email)[count(explode('@', $request->email)) - 1] ?? null;
        $matched_domain = array_search($user_domain, $domains->pluck('dominio')->toArray());

        if ($matched_domain === false) {
            return response()->json([
                'message' => __('messages.login_unauthorized_domain'),
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if ($user && $user->type === User::USER_TYPE_LOCAL) {
            if (auth()->attempt($request->only('email', 'password'), $request->remember)) {

                if ($user->blocked){
                    return response()->json([
                        'message' => __('messages.login_user_blocked'),
                    ], 403);
                }

                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => __('messages.login_success'),
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => UserShowResource::make($user->load('roles', 'permissoes')),
                ], 200);
            } else {
                return response()->json([
                    'message' => __('messages.login_invalid_credentials'),
                ], 401);
            }
        }

        $response = $this->requestIPLeiriaAuth($request->email, $request->password);

        if ($response < 0) {
            return response()->json([
                'message' => __('messages.login_auth_failed'),
            ], 401);
        }

        if ($response === 0) {
            return response()->json([
                'message' => __('messages.login_invalid_credentials'),
            ], 401);
        }

        if ($user) {
            if ($user->blocked){
                return response()->json([
                    'message' => __('messages.login_user_blocked'),
                ], 403);
            }
        } else {
            $name = explode('@', $request->email)[0];
            $name = str_replace('.', ' ', $name);
            $user = User::create([
                'name' => $name,
                'email' => $request->email,
                'type' => User::USER_TYPE_REMOTE,
            ]);

            $id_role= $domains[$matched_domain]['id_role'] ?? null;

            UserRole::create([
                'id_user' => $user->id,
                'id_role' => $id_role,
            ]);
        }

        return response()->json([
            'message' => __('messages.login_success'),
            'access_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'user' => UserShowResource::make($user->load('roles', 'permissoes')),
        ], 200);

    }
}
