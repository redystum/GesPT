<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegexController;
use App\Http\Controllers\Admin\PermitionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EquivalencesController;
use App\Http\Controllers\OpcoesController;
use App\Http\Controllers\PlanoEstudoController;
use App\Http\Controllers\UnidadeCurricularController;
use App\Http\Controllers\WizardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);;

Route::get('/compare', [EquivalencesController::class, 'compare']);

// Routes for courses
Route::apiResource('cursos', CourseController::class);
Route::get('/cursos/{curso}/latest-plan', [CourseController::class, 'getLatestPlan']);

// Routes for Planos de Estudo
Route::apiResource('planos-estudo', PlanoEstudoController::class)
    ->parameters(['planos-estudo' => 'planoEstudo']);
Route::get('/planos-estudo/{planoEstudo}/ucs', [PlanoEstudoController::class, 'getUcs']);

// Routes for UCs
Route::apiResource('ucs', UnidadeCurricularController::class);

// Routes for Opções
Route::apiResource('opcoes', OpcoesController::class)
    ->parameters(['opcoes' => 'opcoao']);

// Route for wizard
Route::post('wizard', [WizardController::class, 'store']);

// route for testing admin regex builder
Route::prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::prefix('login')->group(function () {
        Route::get('/', [LoginController::class, 'get']);
        Route::post('/', [LoginController::class, 'store']);
    });

    Route::prefix('regex/')->group(function () {
        Route::post('test', [RegexController::class, 'testRegex']);
        Route::post('update', [RegexController::class, 'updateRegex']);
        Route::get('get', [RegexController::class, 'get']);
    });
    Route::get('roles/all', [RolesController::class, 'all']);
    Route::apiResource('roles', RolesController::class);
    Route::get('permissions', [PermitionsController::class, 'index']);

});


Route::any('/', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
        ];
    });
    return response()->json($routes);
});
