<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            [
                'nombre' => 'Super Administrador',
                'slug' => 'super_admin',
                'descripcion' => 'Control total del sistema.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Supervisor',
                'slug' => 'supervisor',
                'descripcion' => 'Supervisa todos los reportes.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Jefe de Departamento',
                'slug' => 'jefe_departamento',
                'descripcion' => 'Gestiona únicamente su departamento.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Recepcionista',
                'slug' => 'recepcionista',
                'descripcion' => 'Registra llamadas y reportes.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
