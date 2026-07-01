<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermitionCategoriesResource;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermitionsController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = $request->user();
                if (!$user->hasPermission(['roles.view_any', 'roles.create', 'roles.update', 'users.view_any', 'users.create', 'users.update'])) {
                    abort(403, 'Unauthorized.');
                }
                return $next($request);
            }, only: ['index']),
        ];
    }
    //
    public function index()
    {
        $perms = Category::has('permissoes')->with('permissoes')->get();
        return response()->json([
            'data' => PermitionCategoriesResource::collection($perms),
        ], 200);
    }
}
