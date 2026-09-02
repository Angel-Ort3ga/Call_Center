<?php

namespace App\Services;

use App\Models\Departamento;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Obtener usuarios.
     */
    public function index(array $filters = [])
    {
        $query = User::with([
            'role',
            'departamento',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Búsqueda
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['search'])) {

            $search = trim($filters['search']);

            $query->where(function ($q) use ($search) {

                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Rol
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['role_id'])) {

            $query->where(
                'role_id',
                $filters['role_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Departamento
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['departamento_id'])) {

            $query->where(
                'departamento_id',
                $filters['departamento_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['activo']) &&
            $filters['activo'] !== ''
        ) {

            $query->where(
                'activo',
                filter_var(
                    $filters['activo'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Paginación
        |--------------------------------------------------------------------------
        */

        $perPage = (int) (
            $filters['per_page'] ?? 10
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtener usuario.
     */
    public function show(int $id): User
    {
        return User::with([
            'role',
            'departamento',
        ])->findOrFail($id);
    }

    /**
     * Obtener roles.
     */
    public function roles()
    {
        return Role::orderBy('id')->get();
    }

    /**
     * Obtener departamentos.
     */
    public function departamentos()
    {
        return Departamento::orderBy('nombre')->get();
    }

    /**
     * Crear usuario.
     */
    public function store(array $data): User
    {
        $role = Role::findOrFail(
            $data['role_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Departamento
        |--------------------------------------------------------------------------
        |
        | El departamento solamente es obligatorio
        | para el Jefe de departamento.
        |
        */

        if ($role->slug !== 'jefe_departamento') {

            $data['departamento_id'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Contraseña
        |--------------------------------------------------------------------------
        */

        $data['password'] = Hash::make(
            $data['password']
        );

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        if (!isset($data['activo'])) {
            $data['activo'] = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear usuario
        |--------------------------------------------------------------------------
        */

        $user = User::create($data);

        return $user->fresh([
            'role',
            'departamento',
        ]);
    }

    /**
     * Actualizar usuario.
     */
    public function update(
        User $user,
        array $data
    ): User {

        if (isset($data['role_id'])) {

            $role = Role::findOrFail(
                $data['role_id']
            );

            if (
                $role->slug !== 'jefe_departamento'
            ) {

                $data['departamento_id'] = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Contraseña
        |--------------------------------------------------------------------------
        */

        if (!empty($data['password'])) {

            $data['password'] = Hash::make(
                $data['password']
            );
        } else {

            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh([
            'role',
            'departamento',
        ]);
    }

    /**
     * Activar / desactivar.
     */
    public function toggleStatus(
        User $user
    ): User {

        $user->update([
            'activo' => !$user->activo,
        ]);

        return $user->fresh([
            'role',
            'departamento',
        ]);
    }

    /**
     * Desactivar usuario.
     */
    public function destroy(
        User $user
    ): User {

        $user->update([
            'activo' => false,
        ]);

        return $user->fresh([
            'role',
            'departamento',
        ]);
    }
}
