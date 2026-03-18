<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home', [HomeController::class, 'index']);

// Routes for courses
Route::get('/course', [CourseController::class, 'index']);
Route::get('/course/{curso}', [CourseController::class, 'show']);
Route::post('/course', [CourseController::class, 'store']);
Route::put('/course/{curso}', [CourseController::class, 'update']);
Route::delete('/course/{curso}', [CourseController::class, 'destroy']);
