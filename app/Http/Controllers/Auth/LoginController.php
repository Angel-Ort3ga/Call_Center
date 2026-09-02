<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $login = $this->authService->login(
            $request->email,
            $request->password
        );

        if (!$login) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión correcto.',
            'token' => $login['token'],
            'user' => [
                'id' => $login['user']->id,
                'nombre' => $login['user']->nombre,
                'apellido' => $login['user']->apellido,
                'email' => $login['user']->email,
                'role' => $login['user']->role->slug,
                'departamento' => $login['user']->departamento->nombre,
            ]
        ]);
    }

    /**
     * Usuario autenticado
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('role', 'departamento');

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'role' => $user->role->slug,
            'departamento' => $user->departamento->nombre,
        ]);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.'
        ]);
    }
}
