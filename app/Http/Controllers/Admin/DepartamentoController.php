<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DepartamentoController extends Controller
{
    /**
     * Listar departamentos.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Departamento::with('responsable');

        /*
        |--------------------------------------------------------------------------
        | Búsqueda
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('extension', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Estado
        |--------------------------------------------------------------------------
        */

        if ($request->filled('activo')) {

            $query->where(
                'activo',
                $request->boolean('activo')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Orden y paginación
        |--------------------------------------------------------------------------
        */

        $departamentos = $query
            ->orderBy('nombre')
            ->paginate(10);

        return response()->json([
            'success' => true,

            'data' => $departamentos,
        ]);
    }


    /**
     * Mostrar un departamento.
     */
    public function show(int $id): JsonResponse
    {
        $departamento = Departamento::with('responsable')
            ->find($id);

        if (!$departamento) {

            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,

            'data' => $departamento,
        ]);
    }


    /**
     * Mostrar las llamadas de un departamento.
     *
     * Por defecto:
     * - Muestra las llamadas del día actual.
     *
     * Periodos disponibles:
     * - hoy
     * - semana
     * - mes
     * - todo
     * - personalizado
     */
    public function llamadas(
        Request $request,
        int $id
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Buscar departamento
        |--------------------------------------------------------------------------
        */

        $departamento = Departamento::with('responsable')
            ->find($id);

        if (!$departamento) {

            return response()->json([
                'success' => false,
                'message' => 'Departamento no encontrado.',
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Consulta de llamadas
        |--------------------------------------------------------------------------
        */

        $query = $departamento->llamadas()
            ->with([
                'departamento',
                'usuario.role',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Búsqueda
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('folio', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('categoria', 'like', "%{$search}%")
                    ->orWhere('motivo', 'like', "%{$search}%");
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filtro por estado
        |--------------------------------------------------------------------------
        */

        if ($request->filled('estado')) {

            $query->where(
                'estado',
                $request->estado
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filtro por periodo
        |--------------------------------------------------------------------------
        */

        $periodo = $request->input(
            'periodo',
            'hoy'
        );


        switch ($periodo) {

            /*
            |--------------------------------------------------------------------------
            | HOY
            |--------------------------------------------------------------------------
            */

            case 'hoy':

                $query->whereDate(
                    'fecha',
                    Carbon::today()
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ESTA SEMANA
            |--------------------------------------------------------------------------
            */

            case 'semana':

                $inicioSemana =
                    Carbon::now()->startOfWeek();

                $finSemana =
                    Carbon::now()->endOfWeek();

                $query->whereBetween(
                    'fecha',
                    [
                        $inicioSemana->toDateString(),
                        $finSemana->toDateString(),
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | ESTE MES
            |--------------------------------------------------------------------------
            */

            case 'mes':

                $inicioMes =
                    Carbon::now()->startOfMonth();

                $finMes =
                    Carbon::now()->endOfMonth();

                $query->whereBetween(
                    'fecha',
                    [
                        $inicioMes->toDateString(),
                        $finMes->toDateString(),
                    ]
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | TODO
            |--------------------------------------------------------------------------
            */

            case 'todo':

                // No agregamos filtro de fecha.

                break;


            /*
            |--------------------------------------------------------------------------
            | PERSONALIZADO
            |--------------------------------------------------------------------------
            */

            case 'personalizado':

                if (
                    $request->filled('fecha_desde') &&
                    $request->filled('fecha_hasta')
                ) {

                    $request->validate([
                        'fecha_desde' => [
                            'date',
                        ],

                        'fecha_hasta' => [
                            'date',
                            'after_or_equal:fecha_desde',
                        ],
                    ]);


                    $query->whereBetween(
                        'fecha',
                        [
                            $request->fecha_desde,
                            $request->fecha_hasta,
                        ]
                    );
                }

                break;


            /*
            |--------------------------------------------------------------------------
            | PERIODO DESCONOCIDO
            |--------------------------------------------------------------------------
            */

            default:

                $query->whereDate(
                    'fecha',
                    Carbon::today()
                );

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | Orden
        |--------------------------------------------------------------------------
        */

        $llamadas = $query
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(15);


        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [
                'departamento' => $departamento,

                'llamadas' => $llamadas,
            ],
        ]);
    }


    /**
     * Crear departamento.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'codigo' => [
                'required',
                'string',
                'max:20',
                'unique:departamentos,codigo',
            ],

            'extension' => [
                'required',
                'string',
                'max:10',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:255',
            ],

            'responsable_id' => [
                'nullable',
                'exists:users,id',
            ],

            'horario_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'horario_fin' => [
                'required',
                'date_format:H:i',
            ],

            'activo' => [
                'boolean',
            ],

        ]);


        $departamento =
            Departamento::create($validated);


        return response()->json([

            'success' => true,

            'message' =>
            'Departamento creado correctamente.',

            'data' =>
            $departamento->load('responsable'),

        ], 201);
    }


    /**
     * Actualizar departamento.
     */
    public function update(
        Request $request,
        Departamento $departamento
    ): JsonResponse {

        $validated = $request->validate([

            'nombre' => [
                'required',
                'string',
                'max:100',
            ],

            'codigo' => [
                'required',
                'string',
                'max:20',

                Rule::unique(
                    'departamentos',
                    'codigo'
                )->ignore($departamento->id),
            ],

            'extension' => [
                'required',
                'string',
                'max:10',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'correo' => [
                'nullable',
                'email',
                'max:255',
            ],

            'responsable_id' => [
                'nullable',
                'exists:users,id',
            ],

            'horario_inicio' => [
                'required',
                'date_format:H:i',
            ],

            'horario_fin' => [
                'required',
                'date_format:H:i',
            ],

            'activo' => [
                'boolean',
            ],

        ]);


        $departamento->update(
            $validated
        );


        return response()->json([

            'success' => true,

            'message' =>
            'Departamento actualizado correctamente.',

            'data' =>
            $departamento
                ->fresh()
                ->load('responsable'),

        ]);
    }


    /**
     * Cambiar estado.
     */
    public function toggleStatus(
        Departamento $departamento
    ): JsonResponse {

        $departamento->update([

            'activo' =>
            !$departamento->activo,

        ]);


        return response()->json([

            'success' => true,

            'message' =>
            $departamento->activo
                ? 'Departamento activado correctamente.'
                : 'Departamento desactivado correctamente.',

            'data' =>
            $departamento
                ->fresh()
                ->load('responsable'),

        ]);
    }
}
