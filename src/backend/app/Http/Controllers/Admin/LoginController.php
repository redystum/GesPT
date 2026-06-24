<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoginRequest;
use App\Http\Resources\LoginDominiosResource;
use App\Models\LoginDominios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoginController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . LoginDominios::class, only: ['get']),
            new Middleware(function ($request, $next) {
                $user = $request->user();
                if (!$user->hasPermission(['login_dominios.create', 'login_dominios.update', 'login_dominios.delete'])) {
                    abort(403, 'Unauthorized.');
                }
                return $next($request);
            }, only: ['store']),
        ];
    }
    public function get()
    {
        $domains = LoginDominios::with('role')->get();
        return response()->json([
            'data' => LoginDominiosResource::collection($domains)
        ]);
    }

    public function store(StoreLoginRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $requestDomains = collect($validated)->pluck('dominio')->all();

            // Delete domains that are not in the request, but keep gespt.local
            LoginDominios::whereNotIn('dominio', $requestDomains)
                ->where('dominio', '!=', 'gespt.local')
                ->delete();

            // Update or create domains
            foreach ($validated as $item) {
                LoginDominios::updateOrCreate(
                    ['dominio' => $item['dominio']],
                    ['id_role' => $item['role_id'] ?? null]
                );
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => __('messages.login_domains_update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'data' => LoginDominiosResource::collection(LoginDominios::with('role')->get())
        ], 201);
    }
}
