<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoginRequest;
use App\Http\Resources\LoginDominiosResource;
use App\Models\LoginDominios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
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
