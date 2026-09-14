<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Llamada;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard general del sistema.
     */
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | PERIODO
        |--------------------------------------------------------------------------
        */

        $periodo = $request->input('periodo', 'hoy');

        $fechaInicio = null;
        $fechaFin = null;

        switch ($periodo) {

            case 'semana':

                $fechaInicio = now()->startOfWeek();
                $fechaFin = now()->endOfWeek();

                break;

            case 'mes':

                $fechaInicio = now()->startOfMonth();
                $fechaFin = now()->endOfMonth();

                break;

            case 'personalizado':

                $fechaInicioInput = $request->input('fecha_inicio');
                $fechaFinInput = $request->input('fecha_fin');

                if ($fechaInicioInput && $fechaFinInput) {

                    try {

                        $fechaInicio = Carbon::parse(
                            $fechaInicioInput
                        )->startOfDay();

                        $fechaFin = Carbon::parse(
                            $fechaFinInput
                        )->endOfDay();
                    } catch (\Throwable $e) {

                        return response()->json([
                            'success' => false,
                            'message' => 'Las fechas proporcionadas no son válidas.',
                        ], 422);
                    }
                } else {

                    return response()->json([
                        'success' => false,
                        'message' => 'Debes proporcionar fecha_inicio y fecha_fin.',
                    ], 422);
                }

                break;

            case 'hoy':

            default:

                $periodo = 'hoy';

                $fechaInicio = now()->startOfDay();
                $fechaFin = now()->endOfDay();

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | USUARIOS
        |--------------------------------------------------------------------------
        */

        $usuarios = [
            'total' => User::count(),

            'activos' => User::where(
                'activo',
                true
            )->count(),

            'inactivos' => User::where(
                'activo',
                false
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | DEPARTAMENTOS
        |--------------------------------------------------------------------------
        */

        $departamentos = [
            'total' => Departamento::count(),

            'activos' => Departamento::where(
                'activo',
                true
            )->count(),

            'inactivos' => Departamento::where(
                'activo',
                false
            )->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | CONSULTA BASE DE LLAMADAS
        |--------------------------------------------------------------------------
        */

        $llamadasQuery = Llamada::query()
            ->whereBetween(
                'fecha',
                [
                    $fechaInicio->toDateString(),
                    $fechaFin->toDateString(),
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | PERMISOS SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if ($usuario) {

            if (
                $usuario->esSuperAdmin() ||
                $usuario->esSupervisor()
            ) {

                // Puede consultar todas las llamadas.

            } elseif ($usuario->esJefeDepartamento()) {

                $llamadasQuery->where(
                    'departamento_id',
                    $usuario->departamento_id
                );
            } elseif ($usuario->esRecepcionista()) {

                $llamadasQuery->where(
                    'usuario_id',
                    $usuario->id
                );
            } else {

                // Usuario sin permisos.

                $llamadasQuery->whereRaw(
                    '1 = 0'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RESUMEN DE LLAMADAS
        |--------------------------------------------------------------------------
        */

        $totalLlamadas = (clone $llamadasQuery)
            ->count();

        $enProceso = (clone $llamadasQuery)
            ->where(
                'estado',
                'en_proceso'
            )
            ->count();

        $transferidas = (clone $llamadasQuery)
            ->where(
                'estado',
                'transferida'
            )
            ->count();

        $finalizadas = (clone $llamadasQuery)
            ->where(
                'estado',
                'finalizada'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        $porDepartamento = (clone $llamadasQuery)
            ->select(
                'departamento_id',
                DB::raw('COUNT(*) as total')
            )
            ->with(
                'departamento:id,nombre'
            )
            ->groupBy(
                'departamento_id'
            )
            ->orderByDesc(
                'total'
            )
            ->get()
            ->map(function ($item) {

                return [
                    'departamento_id' => $item->departamento_id,

                    'departamento' =>
                    $item->departamento?->nombre
                        ?? 'Sin departamento',

                    'total' => (int) $item->total,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        $porCategoria = (clone $llamadasQuery)
            ->select(
                'categoria',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                'categoria'
            )
            ->orderByDesc(
                'total'
            )
            ->get()
            ->map(function ($item) {

                return [
                    'categoria' =>
                    $item->categoria
                        ?? 'sin_categoria',

                    'total' => (int) $item->total,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR HORA
        |--------------------------------------------------------------------------
        |
        | Para "hoy" mostramos las llamadas distribuidas por hora.
        |
        | Para semana/mes/personalizado también agrupamos por hora,
        | permitiendo ver cuáles son las horas con mayor actividad.
        |
        */

        $porHora = (clone $llamadasQuery)
            ->select(
                DB::raw('HOUR(hora) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                DB::raw('HOUR(hora)')
            )
            ->orderBy(
                'hora'
            )
            ->get()
            ->map(function ($item) {

                return [
                    'hora' => str_pad(
                        $item->hora,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ) . ':00',

                    'total' => (int) $item->total,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR DÍA
        |--------------------------------------------------------------------------
        |
        | Esta información será utilizada posteriormente para mostrar
        | la evolución de llamadas durante la semana/mes.
        |
        */

        $porDia = (clone $llamadasQuery)
            ->select(
                'fecha',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                'fecha'
            )
            ->orderBy(
                'fecha'
            )
            ->get()
            ->map(function ($item) {

                return [
                    'fecha' => Carbon::parse(
                        $item->fecha
                    )->format('Y-m-d'),

                    'total' => (int) $item->total,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | INFORMACIÓN DEL USUARIO ACTUAL
        |--------------------------------------------------------------------------
        */

        $informacionUsuario = [
            'id' => $usuario?->id,

            'nombre' => $usuario
                ? trim(
                    ($usuario->nombre ?? '') .
                        ' ' .
                        ($usuario->apellido ?? '')
                )
                : null,

            'departamento_id' =>
            $usuario?->departamento_id,
        ];


        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'data' => [

                /*
                |--------------------------------------------------------------------------
                | PERIODO
                |--------------------------------------------------------------------------
                */

                'periodo' => $periodo,

                'fecha_inicio' =>
                $fechaInicio->toDateString(),

                'fecha_fin' =>
                $fechaFin->toDateString(),


                /*
                |--------------------------------------------------------------------------
                | USUARIO
                |--------------------------------------------------------------------------
                */

                'usuario' =>
                $informacionUsuario,


                /*
                |--------------------------------------------------------------------------
                | USUARIOS
                |--------------------------------------------------------------------------
                */

                'usuarios' => $usuarios,


                /*
                |--------------------------------------------------------------------------
                | DEPARTAMENTOS
                |--------------------------------------------------------------------------
                */

                'departamentos' => $departamentos,


                /*
                |--------------------------------------------------------------------------
                | LLAMADAS
                |--------------------------------------------------------------------------
                */

                'llamadas' => [

                    'total' =>
                    $totalLlamadas,

                    'en_proceso' =>
                    $enProceso,

                    'transferidas' =>
                    $transferidas,

                    'finalizadas' =>
                    $finalizadas,
                ],


                /*
                |--------------------------------------------------------------------------
                | ESTADÍSTICAS
                |--------------------------------------------------------------------------
                */

                'estadisticas' => [

                    'por_departamento' =>
                    $porDepartamento,

                    'por_categoria' =>
                    $porCategoria,

                    'por_hora' =>
                    $porHora,

                    'por_dia' =>
                    $porDia,
                ],
            ],
        ]);
    }
}
