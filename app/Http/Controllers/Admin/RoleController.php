<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->where('activo', true)
            ->orderBy('id')
            ->get([
                'id',
                'nombre',
                'slug',
                'descripcion',
                'activo',
            ]);
        return $this->success(
            'Roles obtenidos correctamente.',
            $roles
        );
    }
}
