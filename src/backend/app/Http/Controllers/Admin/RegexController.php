<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestRegexRequest;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RegexController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!$request->user()->hasPermission('login_dominios.view_any')) {
                    abort(403, 'Unauthorized.');
                }
                return $next($request);
            }, only: ['get']),
            new Middleware(function ($request, $next) {
                if (!$request->user()->hasPermission('login_dominios.update')) {
                    abort(403, 'Unauthorized.');
                }
                return $next($request);
            }, only: ['updateRegex', 'testRegex']),
        ];
    }

    private function formatRegex($regex)
    {
        // php regex need slash for delimiters, and u for unicode support
        if (!str_starts_with($regex, '/')) {
            $regex = '/' . $regex;
        }
        if (!str_ends_with($regex, '/')) {
            $regex = $regex . '/';
        }
        if (!str_ends_with($regex, 'u')) {
            $regex = $regex . 'u';
        }
        return $regex;
    }

    public function testRegex(TestRegexRequest $request)
    {
        $request->validated();

        $regex = $this->formatRegex($request->regex);

        $results = [];

        collect($request->test_subjects)->each(function ($subject) use ($regex, &$results) {
            $results[] = [
                'subject' => $subject,
                'valid' => !!preg_match($regex, $subject)
            ];
        });

        return response()->json([
            'results' => $results,
            'regex' => $regex
        ]);
    }

    public function updateRegex(TestRegexRequest $request)
    {
        $val_request = $request->validated();

        $regex = $this->formatRegex($val_request['regex']);

        Configuration::set('login_regex', $val_request);

        return response()->json([
            'regex' => $regex
        ]);
    }

    public function get()
    {
        return response()->json(Configuration::get('login_regex'));
    }
}
