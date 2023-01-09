<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    // testing Routes
    Route::get('/test', function() {
        return response()->json('testing...');
    });
    // auth Routes
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
    // task routes
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::apiResource('/tasks', TaskController::class);
    });
});


