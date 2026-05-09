<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * @param string $email
     * @param string $password
//     * @return int `-1` no url <br> `-2` failed request <br> `0` wrong credentials <br> `1` correct credentials
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

        $response = $this->requestIPLeiriaAuth($request->email, $request->password);

        dd($request->all(), $response);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->type === User::USER_TYPE_LOCAL) {
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
