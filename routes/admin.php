<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')->group(function () {

    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', [LoginController::class, 'user']);

        Route::post('/logout', [LoginController::class, 'logout']);


        Route::get('/usuarios', [
            UserController::class,
            'index'
        ]);

        Route::get('/usuarios/{id}', [
            UserController::class,
            'show'
        ]);

        Route::post('/usuarios', [
            UserController::class,
            'store'
        ]);

        Route::patch('/usuarios/{user}/estado', [
            UserController::class,
            'toggleStatus'
        ]);

        Route::delete('/usuarios/{user}', [
            UserController::class,
            'destroy'
        ]);
    });
});
