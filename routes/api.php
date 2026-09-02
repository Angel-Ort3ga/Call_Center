<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\LlamadaController;
use App\Http\Controllers\Admin\RoleController;


Route::prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Autenticación
    |--------------------------------------------------------------------------
    */

    Route::post('/login', [
        LoginController::class,
        'login'
    ]);


    /*
    |--------------------------------------------------------------------------
    | Rutas protegidas
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Usuario autenticado
        |--------------------------------------------------------------------------
        */

        Route::get('/user', [
            LoginController::class,
            'user'
        ]);

        Route::post('/logout', [
            LoginController::class,
            'logout'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        |
        | Solo Super Administrador y Supervisor.
        |
        */

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->middleware('role:super_admin,supervisor,jefe_departamento,recepcionista');


        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        |
        | Solo Super Administrador.
        |
        */

        Route::middleware('role:super_admin')->group(function () {

            Route::get('/roles', [
                RoleController::class,
                'index'
            ]);

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

            Route::put('/usuarios/{user}', [
                UserController::class,
                'update'
            ]);

            Route::patch('/usuarios/{user}/estado', [
                UserController::class,
                'toggleStatus'
            ]);

            Route::delete('/usuarios/{user}', [
                UserController::class,
                'destroy'
            ]);


            /*
            |--------------------------------------------------------------------------
            | Departamentos - Administración
            |--------------------------------------------------------------------------
            */

            Route::post('/departamentos', [
                DepartamentoController::class,
                'store'
            ]);

            Route::put('/departamentos/{departamento}', [
                DepartamentoController::class,
                'update'
            ]);

            Route::patch('/departamentos/{departamento}/estado', [
                DepartamentoController::class,
                'toggleStatus'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Departamentos - Consulta
        |--------------------------------------------------------------------------
        |
        | Estos roles pueden consultar departamentos.
        |
        */

        Route::middleware(
            'role:super_admin,supervisor,jefe_departamento,recepcionista'
        )->group(function () {

            Route::get('/departamentos', [
                DepartamentoController::class,
                'index'
            ]);

            Route::get('/departamentos/{id}/llamadas', [
                DepartamentoController::class,
                'llamadas'
            ]);

            Route::get('/departamentos/{id}', [
                DepartamentoController::class,
                'show'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Llamadas
        |--------------------------------------------------------------------------
        | Todos los roles pueden consultar llamadas.
        */
        Route::middleware(
            'role:super_admin,supervisor,jefe_departamento,recepcionista'
        )->group(function () {

            Route::get('/llamadas', [
                LlamadaController::class,
                'index'
            ]);

            Route::get('/llamadas/resumen', [
                LlamadaController::class,
                'resumen'
            ]);

            Route::get('/llamadas/{id}', [
                LlamadaController::class,
                'show'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Registro y modificación de llamadas
        |--------------------------------------------------------------------------
        |
        | Por ahora Super Administrador y Recepcionista.
        |
        */

        Route::middleware(
            'role:super_admin,recepcionista'
        )->group(function () {

            Route::post('/llamadas', [
                LlamadaController::class,
                'store'
            ]);

            Route::put('/llamadas/{llamada}', [
                LlamadaController::class,
                'update'
            ]);

            Route::patch('/llamadas/{llamada}/estado', [
                LlamadaController::class,
                'toggleStatus'
            ]);
        });
    });
});
