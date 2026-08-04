<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nombre' => 'Super',
            'apellido' => 'Administrador',
            'username' => 'superadmin',
            'email' => 'admin@callcenter.com',
            'password' => Hash::make('admin123'),
            'telefono' => '5551234567',
            'extension' => '100',
            'foto' => null,
            'role_id' => 1,
            'departamento_id' => 1,
            'activo' => true,
            'new_notifications' => 0,
            'ultimo_acceso' => null,
        ]);
    }
}
