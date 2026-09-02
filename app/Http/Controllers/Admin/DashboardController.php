<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Llamada;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard general.
     *
     * Permisos:
     * - Super Admin: todas las llamadas
     * - Supervisor: todas las llamadas
     * - Jefe de departamento: solo su departamento
     * - Recepcionista: solo sus llamadas
     */
    public function index(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | USUARIO AUTENTICADO
        |--------------------------------------------------------------------------
        */

        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | FECHA DEL DASHBOARD
        |--------------------------------------------------------------------------
        */

        $fecha = $request->input(
            'fecha',
            now()->toDateString()
        );

        /*
        |--------------------------------------------------------------------------
        | USUARIOS
        |--------------------------------------------------------------------------
        |
        | Esta información solamente se muestra de forma general.
        |
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
        ];

        /*
        |--------------------------------------------------------------------------
        | CONSULTA BASE DE LLAMADAS
        |--------------------------------------------------------------------------
        |
        | A partir de esta consulta aplicamos los permisos.
        |
        */

        $llamadasQuery = Llamada::query()
            ->whereDate('fecha', $fecha);

        /*
        |--------------------------------------------------------------------------
        | FILTRO POR ROL
        |--------------------------------------------------------------------------
        */

        if ($usuario) {

            /*
            |------------------------------------------------------------------
            | Super Admin y Supervisor
            |------------------------------------------------------------------
            |
            | Pueden consultar todas las llamadas.
            |
            */

            if (
                $usuario->esSuperAdmin() ||
                $usuario->esSupervisor()
            ) {
                // Sin filtro adicional.
            }

            /*
            |------------------------------------------------------------------
            | Jefe de departamento
            |------------------------------------------------------------------
            */ elseif ($usuario->esJefeDepartamento()) {

                $llamadasQuery->where(
                    'departamento_id',
                    $usuario->departamento_id
                );
            }

            /*
            |------------------------------------------------------------------
            | Recepcionista
            |------------------------------------------------------------------
            */ elseif ($usuario->esRecepcionista()) {

                $llamadasQuery->where(
                    'usuario_id',
                    $usuario->id
                );
            }

            /*
            |------------------------------------------------------------------
            | Cualquier otro usuario
            |------------------------------------------------------------------
            |
            | Por seguridad no podrá visualizar llamadas.
            |
            */ else {

                $llamadasQuery->whereRaw('1 = 0');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LLAMADAS DEL DÍA
        |--------------------------------------------------------------------------
        */

        $totalLlamadas = (clone $llamadasQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR ESTADO
        |--------------------------------------------------------------------------
        */

        $enProceso = (clone $llamadasQuery)
            ->where('estado', 'en_proceso')
            ->count();

        $transferidas = (clone $llamadasQuery)
            ->where('estado', 'transferida')
            ->count();

        $finalizadas = (clone $llamadasQuery)
            ->where('estado', 'finalizada')
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
            ->with('departamento:id,nombre')
            ->groupBy('departamento_id')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {

                return [
                    'departamento_id' => $item->departamento_id,

                    'departamento' =>
                    $item->departamento?->nombre,

                    'total' =>
                    (int) $item->total,
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
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {

                return [
                    'categoria' =>
                    $item->categoria,

                    'total' =>
                    (int) $item->total,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | LLAMADAS POR HORA
        |--------------------------------------------------------------------------
        */

        $porHora = (clone $llamadasQuery)
            ->select(
                DB::raw('HOUR(hora) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('HOUR(hora)'))
            ->orderBy('hora')
            ->get()
            ->map(function ($item) {

                return [
                    'hora' =>
                    str_pad(
                        $item->hora,
                        2,
                        '0',
                        STR_PAD_LEFT
                    ) . ':00',

                    'total' =>
                    (int) $item->total,
                ];
            })
            ->values();

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
                | Fecha
                |--------------------------------------------------------------------------
                */

                'fecha' => $fecha,

                /*
                |--------------------------------------------------------------------------
                | Información del usuario
                |--------------------------------------------------------------------------
                */

                'usuario' => [
                    'id' => $usuario?->id,
                    'nombre' => $usuario?->name,
                    'departamento_id' =>
                    $usuario?->departamento_id,
                ],

                /*
                |--------------------------------------------------------------------------
                | Usuarios
                |--------------------------------------------------------------------------
                */

                'usuarios' => $usuarios,

                /*
                |--------------------------------------------------------------------------
                | Departamentos
                |--------------------------------------------------------------------------
                */

                'departamentos' => $departamentos,

                /*
                |--------------------------------------------------------------------------
                | Llamadas
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
                | Estadísticas
                |--------------------------------------------------------------------------
                */

                'estadisticas' => [

                    'por_departamento' =>
                    $porDepartamento,

                    'por_categoria' =>
                    $porCategoria,

                    'por_hora' =>
                    $porHora,
                ],
            ],
        ]);
    }
}
