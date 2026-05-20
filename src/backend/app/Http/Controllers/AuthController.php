<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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

        $domains = LoginDominios::all();
        $user_domain = explode('@', $request->email)[count(explode('@', $request->email)) - 1] ?? null;
        $matched_domain = array_search($user_domain, $domains->pluck('dominio')->toArray());

        if ($matched_domain === false) {
            return response()->json([
                'message' => 'Unauthorized domain',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        if ($user && $user->type === User::USER_TYPE_LOCAL) {
            if (auth()->attempt($request->only('email', 'password'), $request->remember)) {

                // todo add blocked state here

                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Login successful',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
        }

        $response = $this->requestIPLeiriaAuth($request->email, $request->password);

        if ($response < 0) {
            return response()->json([
                'message' => 'Authentication failed',
            ], 401);
        }

        if ($response === 0) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user) {
            // todo add blocked state here

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            // TODO add user to db and give the role by domain

            $user = User::create([
                'name' => explode('@', $request->email)[0],
                'email' => $request->email,
                'type' => User::USER_TYPE_REMOTE,
            ]);

            $id_role= $domains[$matched_domain]['id_role'] ?? null;

            UserRole::create([
                'id_user' => $user->id,
                'id_role' => $id_role,
            ]);

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ], 200);
        }

    }
}
