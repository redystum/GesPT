<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegexController;
use App\Http\Controllers\Admin\PermitionsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EquivalencesController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\OpcoesController;
use App\Http\Controllers\PlanoEstudoController;
use App\Http\Controllers\UnidadeCurricularController;
use App\Http\Controllers\WizardController;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login'])->middleware('guest');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [MeController::class, 'me']);

    Route::get('/compare', [EquivalencesController::class, 'compare']);

// Routes for courses
    Route::get('/cursos/{curso}/latest-plan', [CourseController::class, 'getLatestPlan']);
    Route::get('/cursos/{curso}/ucs', [CourseController::class, 'ucs']);
    Route::get('/cursos/{curso}/planos', [CourseController::class, 'planos']);
    Route::apiResource('cursos', CourseController::class);

// Routes for Planos de Estudo
Route::get('/planos-estudo/{planoEstudo}/ucs', [PlanoEstudoController::class, 'getUcs']);
Route::apiResource('planos-estudo', PlanoEstudoController::class)
    ->parameters(['planos-estudo' => 'planoEstudo']);

// export/import
Route::get('/planos-estudo/template/{curso}', [PlanoEstudoController::class, 'exportTemplate']);
Route::post('/planos-estudo/import/{curso}', [PlanoEstudoController::class, 'importTemplate']);

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
    Route::get('wizard/{planoEstudo}', [WizardController::class, 'show']);
    Route::put('wizard/{planoEstudo}', [WizardController::class, 'update']);

// route for testing admin regex builder
    Route::prefix('admin')->group(function () {
        Route::post('users/{user}/block', [UserController::class, 'block']);
        Route::post('users/{user}/unblock', [UserController::class, 'unblock']);
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
