<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestRegexRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function testRegex(TestRegexRequest $request)
    {
        $request->validated();

        $regex = $request->regex;

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
        $request->validated();

        $regex = $request->regex;

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

        // TODO: create table for this


        return response()->json([
            'regex' => $regex
        ]);
    }
}
