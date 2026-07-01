<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AuditLogResource;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuditLogController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,' . Activity::class, only: ['index']),
        ];
    }

    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->orderByDesc('id');

        $filter = $request->input('filter');

        if ($filter === 'blocked') {
            $query->where('log_name', 'login_failed')
                ->where('properties->reason', 'blocked');
        } elseif ($filter === 'unauthorized') {
            $query->where('log_name', 'login_failed')
                ->where('properties->reason', 'unauthorized_domain');
        } elseif ($filter === 'failed_login') {
            $query->where('log_name', 'login_failed');
        } elseif ($filter === 'database') {
            $query->whereNotIn('log_name', ['login_failed', 'user_roles', 'user_permissions']);
        } elseif ($filter === 'access_control') {
            $query->whereIn('log_name', ['user_roles', 'user_permissions']);
        }

        $logs = $query->paginate(200);

        return AuditLogResource::collection($logs);
    }
}
