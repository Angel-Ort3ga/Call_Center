<?php

namespace Database\Seeders;

use App\Models\Llamada;
use App\Models\Departamento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LlamadasPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = Departamento::where('activo', true)->get();
        $usuarios = User::where('activo', true)->get();

        if ($departamentos->isEmpty()) {
            $this->command->error(
                'No hay departamentos activos para crear las llamadas.'
            );

            return;
        }

        if ($usuarios->isEmpty()) {
            $this->command->error(
                'No hay usuarios activos para crear las llamadas.'
            );

            return;
        }

        $categorias = [
            'informacion',
            'queja',
            'tramite',
            'soporte',
        ];

        $estados = [
            'en_proceso',
            'transferida',
            'finalizada',
        ];

        $motivos = [
            'Solicitud de información sobre un trámite.',
            'Consulta sobre requisitos.',
            'Seguimiento de una solicitud.',
            'Queja sobre el servicio recibido.',
            'Solicitud de orientación.',
            'Consulta sobre horarios de atención.',
            'Problema con un trámite.',
            'Solicitud de información general.',
            'Seguimiento de documentación.',
            'Consulta sobre servicios disponibles.',
        ];

        $nombres = [
            'Juan Pérez',
            'María González',
            'Carlos Ramírez',
            'Ana López',
            'Luis Hernández',
            'Sofía Martínez',
            'José García',
            'Laura Torres',
            'Miguel Rodríguez',
            'Daniela Flores',
            'Fernando Sánchez',
            'Gabriela Cruz',
        ];

        $ahora = Carbon::now();

        for ($i = 1; $i <= 40; $i++) {

            $fecha = $ahora->copy()->subDays(rand(0, 180));

            $hora = sprintf(
                '%02d:%02d:00',
                rand(8, 17),
                rand(0, 59)
            );

            $estado = $estados[array_rand($estados)];

            $transferidaAt = null;
            $finalizadaAt = null;

            if (
                $estado === 'transferida' ||
                $estado === 'finalizada'
            ) {
                $transferidaAt = $fecha->copy()
                    ->setTimeFromTimeString($hora)
                    ->addMinutes(rand(5, 30));
            }

            if ($estado === 'finalizada') {
                $finalizadaAt = $transferidaAt->copy()
                    ->addMinutes(rand(10, 60));
            }

            do {
                $folio = sprintf(
                    'CALL-%s-%04d',
                    $fecha->format('Ymd'),
                    rand(1000, 9999)
                );
            } while (Llamada::where('folio', $folio)->exists());

            Llamada::create([
                'folio' => $folio,
                'fecha' => $fecha->format('Y-m-d'),
                'hora' => $hora,
                'telefono' => '612' . rand(1000000, 9999999),
                'nombre' => $nombres[array_rand($nombres)],
                'motivo' => $motivos[array_rand($motivos)],
                'categoria' => $categorias[array_rand($categorias)],
                'departamento_id' => $departamentos->random()->id,
                'usuario_id' => $usuarios->random()->id,
                'estado' => $estado,
                'observaciones' => 'Llamada generada para pruebas del sistema.',
                'transferida_at' => $transferidaAt,
                'finalizada_at' => $finalizadaAt,
            ]);
        }

        $this->command->info(
            'Se crearon 40 llamadas de prueba correctamente.'
        );
    }
}
