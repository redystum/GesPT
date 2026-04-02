<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EquivalencesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanoEstudoController;
use App\Http\Controllers\UnidadeCurricularController;
use App\Http\Controllers\OpcoesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home', [EquivalencesController::class, 'index']);

// Routes for courses
Route::apiResource('cursos', CourseController::class);

// Routes for Planos de Estudo
Route::apiResource('planos-estudo', PlanoEstudoController::class)
    ->parameters(['planos-estudo' => 'planoEstudo']);

// Routes for UCs
Route::apiResource('ucs', UnidadeCurricularController::class);

// Routes for Opções
Route::apiResource('opcoes', OpcoesController::class)
    ->parameters(['opcoes' => 'opcoao']);

// Routes for Users
Route::apiResource('users', UserController::class);

Route::any('/', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
        ];
    });
    return response()->json($routes);
});
