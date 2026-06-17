<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function me(Request $request)
    {
        return response()->json($request->user()->allPermissoes());
    }
}
