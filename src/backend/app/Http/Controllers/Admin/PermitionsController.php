<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PermitionCategoriesResource;
use App\Models\Category;

class PermitionsController extends Controller
{
    //
    public function index()
    {
        $perms = Category::has('permissoes')->with('permissoes')->get();
        return response()->json([
            'data' => PermitionCategoriesResource::collection($perms),
        ], 200);
    }
}
