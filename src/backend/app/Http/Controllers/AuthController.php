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
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
                activity()
                    ->useLog('login_failed')
                    ->withProperties([
                        'email' => $request->email,
                        'reason' => 'unauthorized_domain'
                    ])
                    ->log("Failed login: unauthorized domain");

                return response()->json([
                    'message' => __('messages.login_unauthorized_domain'),
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            if ($user && $user->type === User::USER_TYPE_LOCAL) {
                if (auth()->attempt($request->only('email', 'password'), $request->remember)) {

                    if ($user->blocked){
                        activity()
                            ->performedOn($user)
                            ->useLog('login_failed')
                            ->withProperties([
                                'email' => $request->email,
                                'user_name' => $user->name,
                                'reason' => 'blocked'
                            ])
                            ->log("Failed login: blocked user");

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
                    activity()
                        ->useLog('login_failed')
                        ->withProperties([
                            'email' => $request->email,
                            'reason' => 'invalid_credentials'
                        ])
                        ->log("Failed login: invalid credentials");

                    return response()->json([
                        'message' => __('messages.login_invalid_credentials'),
                    ], 401);
                }
            }

            $response = $this->requestIPLeiriaAuth($request->email, $request->password);

            if ($response < 0) {
                activity()
                    ->useLog('login_failed')
                    ->withProperties([
                        'email' => $request->email,
                        'reason' => 'auth_failed'
                    ])
                    ->log("Failed login: system auth failed");

                return response()->json([
                    'message' => __('messages.login_auth_failed'),
                ], 401);
            }

            if ($response === 0) {
                activity()
                    ->useLog('login_failed')
                    ->withProperties([
                        'email' => $request->email,
                        'reason' => 'invalid_credentials'
                    ])
                    ->log("Failed login: invalid credentials");

                return response()->json([
                    'message' => __('messages.login_invalid_credentials'),
                ], 401);
            }

            if ($user) {
                if ($user->blocked){
                    activity()
                        ->performedOn($user)
                        ->useLog('login_failed')
                        ->withProperties([
                            'email' => $request->email,
                            'user_name' => $user->name,
                            'reason' => 'blocked'
                        ])
                        ->log("Failed login: blocked user");

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

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();

            // Check if user exists and is local
            if ($user && $user->type === User::USER_TYPE_LOCAL) {
                $recipient = $user->recovery_email ?: $user->email;

                if ($recipient) {
                    // Generate a token
                    $token = Str::random(60);

                    DB::table('password_reset_tokens')->updateOrInsert(
                        ['email' => $user->email],
                        [
                            'token' => Hash::make($token),
                            'created_at' => now()
                        ]
                    );

                    Mail::to($recipient)->send(new ResetPasswordMail($user, $token));
                }
            }

            // Return a generic response for security to avoid email enumeration
            return response()->json([
                'message' => __('messages.password_recovery_sent'),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user || $user->type !== User::USER_TYPE_LOCAL) {
                return response()->json([
                    'message' => __('messages.password_reset_token_invalid'),
                ], 400);
            }

            $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

            if (!$record || !Hash::check($request->token, $record->token)) {
                return response()->json([
                    'message' => __('messages.password_reset_token_invalid'),
                ], 400);
            }

            // Check if token is expired (e.g. 60 minutes)
            if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'message' => __('messages.password_reset_token_invalid'),
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Clean up token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json([
                'message' => __('messages.password_reset_success'),
            ], 200);
    }
}
