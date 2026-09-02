<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Listar usuarios.
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->userService->index(
            $request->only([
                'search',
                'role_id',
                'departamento_id',
                'activo',
                'per_page',
            ])
        );

        return $this->success(
            'Usuarios obtenidos correctamente.',
            $users
        );
    }

    /**
     * Mostrar usuario.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->show($id);

        return $this->success(
            'Usuario obtenido correctamente.',
            $user
        );
    }

    /**
     * Obtener roles.
     */
    public function roles(): JsonResponse
    {
        return $this->success(
            'Roles obtenidos correctamente.',
            $this->userService->roles()
        );
    }

    /**
     * Obtener departamentos.
     */
    public function departamentos(): JsonResponse
    {
        return $this->success(
            'Departamentos obtenidos correctamente.',
            $this->userService->departamentos()
        );
    }

    /**
     * Crear usuario.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store(
            $request->validated()
        );

        return $this->success(
            'Usuario creado correctamente.',
            $user,
            201
        );
    }

    /**
     * Actualizar usuario.
     */
    public function update(
        UpdateUserRequest $request,
        \App\Models\User $user
    ): JsonResponse {

        $updatedUser = $this->userService->update(
            $user,
            $request->validated()
        );

        return $this->success(
            'Usuario actualizado correctamente.',
            $updatedUser
        );
    }

    /**
     * Activar / desactivar usuario.
     */
    public function toggleStatus(
        \App\Models\User $user
    ): JsonResponse {

        $user = $this->userService->toggleStatus($user);

        return $this->success(
            $user->activo
                ? 'Usuario activado correctamente.'
                : 'Usuario desactivado correctamente.',
            $user
        );
    }

    /**
     * Desactivar usuario.
     */
    public function destroy(
        \App\Models\User $user
    ): JsonResponse {

        $user = $this->userService->destroy($user);

        return $this->success(
            'Usuario desactivado correctamente.',
            $user
        );
    }
}
