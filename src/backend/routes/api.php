<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanoEstudoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home', [HomeController::class, 'index']);

// Routes for courses
Route::apiResource('cursos', CourseController::class);

// Routes for Planos de Estudo
Route::apiResource('planos-estudo', PlanoEstudoController::class);
