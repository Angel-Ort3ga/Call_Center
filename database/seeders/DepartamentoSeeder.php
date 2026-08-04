<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        Departamento::insert([

            [
                'nombre' => 'Administración',
                'codigo' => 'ADM',
                'extension' => '100',
                'correo' => 'administracion@callcenter.com',
                //'responsable_id' => null,
                'horario_inicio' => '08:00:00',
                'horario_fin' => '18:00:00',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Recepción',
                'codigo' => 'REC',
                'extension' => '101',
                'correo' => 'recepcion@callcenter.com',
                //'responsable_id' => null,
                'horario_inicio' => '08:00:00',
                'horario_fin' => '18:00:00',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Soporte Técnico',
                'codigo' => 'SOP',
                'extension' => '102',
                'correo' => 'soporte@callcenter.com',
                //'responsable_id' => null,
                'horario_inicio' => '08:00:00',
                'horario_fin' => '18:00:00',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'nombre' => 'Recursos Humanos',
                'codigo' => 'RH',
                'extension' => '103',
                'correo' => 'rh@callcenter.com',
                //'responsable_id' => null,
                'horario_inicio' => '08:00:00',
                'horario_fin' => '18:00:00',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}
