<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // testing Routes
    Route::get('/test', function() {
        return response()->json('testing...');
    });
    // auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        });
    });
    // task routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::resource('/tasks', TaskController::class);
    });
});


