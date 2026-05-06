<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestRegexRequest;
use App\Models\Configuration;
use Illuminate\Http\Request;

class LoginController extends Controller
{

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
