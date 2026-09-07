
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Departamento;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::where(
            'slug',
            'super_admin'
        )->firstOrFail();

        $supervisor = Role::where(
            'slug',
            'supervisor'
        )->firstOrFail();

        $jefeDepartamento = Role::where(
            'slug',
            'jefe_departamento'
        )->firstOrFail();

        $recepcionista = Role::where(
            'slug',
            'recepcionista'
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Departamentos
        |--------------------------------------------------------------------------
        */

        $administracion = Departamento::where(
            'codigo',
            'ADM'
        )->firstOrFail();

        $recepcion = Departamento::where(
            'codigo',
            'REC'
        )->firstOrFail();

        $soporte = Departamento::where(
            'codigo',
            'SOP'
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Super Administrador
        |--------------------------------------------------------------------------
        */

        User::create([
            'nombre' => 'Super',
            'apellido' => 'Administrador',
            'username' => 'superadmin',
            'email' => 'admin@callcenter.com',
            'password' => Hash::make('admin123'),
            'telefono' => '5551234567',
            'extension' => '100',
            'foto' => null,
            'role_id' => $superAdmin->id,
            'departamento_id' => $administracion->id,
            'activo' => true,
            'new_notifications' => 0,
            'ultimo_acceso' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Supervisor
        |--------------------------------------------------------------------------
        */

        User::create([
            'nombre' => 'Supervisor',
            'apellido' => 'General',
            'username' => 'supervisor',
            'email' => 'supervisor@callcenter.com',
            'password' => Hash::make('supervisor123'),
            'telefono' => '5551234568',
            'extension' => '101',
            'foto' => null,
            'role_id' => $supervisor->id,
            'departamento_id' => $administracion->id,
            'activo' => true,
            'new_notifications' => 0,
            'ultimo_acceso' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Jefe de Departamento
        |--------------------------------------------------------------------------
        */

        User::create([
            'nombre' => 'Jefe',
            'apellido' => 'Soporte',
            'username' => 'jefe.soporte',
            'email' => 'jefe@callcenter.com',
            'password' => Hash::make('jefe12345'),
            'telefono' => '5551234569',
            'extension' => '102',
            'foto' => null,
            'role_id' => $jefeDepartamento->id,
            'departamento_id' => $soporte->id,
            'activo' => true,
            'new_notifications' => 0,
            'ultimo_acceso' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Recepcionista
        |--------------------------------------------------------------------------
        */

        User::create([
            'nombre' => 'Recepcionista',
            'apellido' => 'General',
            'username' => 'recepcionista',
            'email' => 'recepcion@callcenter.com',
            'password' => Hash::make('recepcion123'),
            'telefono' => '5551234570',
            'extension' => '103',
            'foto' => null,
            'role_id' => $recepcionista->id,
            'departamento_id' => $recepcion->id,
            'activo' => true,
            'new_notifications' => 0,
            'ultimo_acceso' => null,
        ]);
    }
}
