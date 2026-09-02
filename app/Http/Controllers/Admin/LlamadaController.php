<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Llamada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LlamadaController extends Controller
{
    /**
     * Consulta base de llamadas respetando los permisos del usuario.
     */
    private function consultaPermitida()
    {
        $usuario = Auth::user();

        $query = Llamada::query();

        /*
    |--------------------------------------------------------------------------
    | Super Administrador y Supervisor
    |--------------------------------------------------------------------------
    |
    | Pueden consultar todas las llamadas.
    |
    */

        if (
            $usuario->esSuperAdmin() ||
            $usuario->esSupervisor()
        ) {
            return $query;
        }


        /*
    |--------------------------------------------------------------------------
    | Jefe de departamento
    |--------------------------------------------------------------------------
    |
    | Solo puede consultar llamadas de su departamento.
    |
    */

        if ($usuario->esJefeDepartamento()) {

            return $query->where(
                'departamento_id',
                $usuario->departamento_id
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Recepcionista
    |--------------------------------------------------------------------------
    |
    | Solo puede consultar llamadas que él registró.
    |
    */

        if ($usuario->esRecepcionista()) {

            return $query->where(
                'usuario_id',
                $usuario->id
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Usuario sin permisos
    |--------------------------------------------------------------------------
    */

        return $query->whereRaw('1 = 0');
    }

    /**
     * Listar llamadas.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->consultaPermitida()->with([
            'departamento',
            'usuario.role',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buscar
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
        | Filtrar por departamento
        |--------------------------------------------------------------------------
        */

        $usuario = Auth::user();

        if ($usuario->esJefeDepartamento()) {

            $query->where(
                'departamento_id',
                $usuario->departamento_id
            );
        } elseif ($usuario->esRecepcionista()) {

            $query->where(
                'usuario_id',
                $usuario->id
            );
        } elseif ($request->filled('departamento_id')) {

            $query->where(
                'departamento_id',
                $request->departamento_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filtrar por estado
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
        | Filtrar por categoría
        |--------------------------------------------------------------------------
        */

        if ($request->filled('categoria')) {

            $query->where(
                'categoria',
                $request->categoria
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filtrar por fecha
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fecha')) {

            $query->whereDate(
                'fecha',
                $request->fecha
            );
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

        return response()->json([
            'success' => true,
            'data' => $llamadas,
        ]);
    }


    /**
     * Resumen general de llamadas.
     */
    public function resumen(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Fechas
        |--------------------------------------------------------------------------
        |
        | Por defecto mostramos el resumen del día actual.
        |
        */

        $fecha = $request->input(
            'fecha',
            now()->toDateString()
        );

        /*
        |--------------------------------------------------------------------------
        | Consulta base
        |--------------------------------------------------------------------------
        */

        $query = $this->consultaPermitida()
            ->whereDate('fecha', $fecha);

        /*
        |--------------------------------------------------------------------------
        | Resumen por estado
        |--------------------------------------------------------------------------
        */

        $total = (clone $query)->count();

        $enProceso = (clone $query)
            ->where('estado', 'en_proceso')
            ->count();

        $transferidas = (clone $query)
            ->where('estado', 'transferida')
            ->count();

        $finalizadas = (clone $query)
            ->where('estado', 'finalizada')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Llamadas por departamento
        |--------------------------------------------------------------------------
        */

        $porDepartamento = (clone $query)
            ->join(
                'departamentos',
                'llamadas.departamento_id',
                '=',
                'departamentos.id'
            )
            ->select(
                'departamentos.id',
                'departamentos.nombre',
                DB::raw('COUNT(llamadas.id) as total')
            )
            ->groupBy(
                'departamentos.id',
                'departamentos.nombre'
            )
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Llamadas por categoría
        |--------------------------------------------------------------------------
        */

        $porCategoria = (clone $query)
            ->select(
                'categoria',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Motivos más frecuentes
        |--------------------------------------------------------------------------
        */

        $motivos = (clone $query)
            ->select(
                'motivo',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('motivo')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Llamadas por hora
        |--------------------------------------------------------------------------
        */

        $porHora = (clone $query)
            ->select(
                DB::raw(
                    "HOUR(hora) as hora"
                ),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(
                DB::raw('HOUR(hora)')
            )
            ->orderBy('hora')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'fecha' => $fecha,

                'resumen' => [
                    'total' => $total,
                    'en_proceso' => $enProceso,
                    'transferidas' => $transferidas,
                    'finalizadas' => $finalizadas,
                ],

                'por_departamento' => $porDepartamento,

                'por_categoria' => $porCategoria,

                'motivos' => $motivos,

                'por_hora' => $porHora,
            ],
        ]);
    }

    /**
     * Mostrar una llamada específica.
     */
    public function show(int $id): JsonResponse
    {
        $llamada = Llamada::with([
            'departamento',
            'usuario.role',
        ])->find($id);

        if (!$llamada) {

            return response()->json([
                'success' => false,
                'message' => 'Llamada no encontrada.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar permisos
        |--------------------------------------------------------------------------
        */

        if (!$this->puedeAcceder($llamada)) {

            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para acceder a esta llamada.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $llamada,
        ]);
    }

    /**
     * Registrar una nueva llamada.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([

            'telefono' => [
                'required',
                'string',
                'max:20',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'motivo' => [
                'required',
                'string',
            ],

            'categoria' => [
                'required',
                'string',
                'max:50',
            ],

            'departamento_id' => [
                'required',
                'exists:departamentos,id',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Usuario autenticado
        |--------------------------------------------------------------------------
        */

        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Crear llamada
        |--------------------------------------------------------------------------
        */

        $llamada = DB::transaction(function () use (
            $validated,
            $usuario
        ) {

            $ahora = now();

            $folio = $this->generarFolio();

            return Llamada::create([

                'folio' => $folio,

                'fecha' => $ahora->toDateString(),

                'hora' => $ahora->format('H:i:s'),

                'telefono' => $validated['telefono'],

                'nombre' => $validated['nombre'],

                'motivo' => $validated['motivo'],

                'categoria' => $validated['categoria'],

                'departamento_id' =>
                $validated['departamento_id'],

                'usuario_id' => $usuario->id,

                'estado' => 'en_proceso',

                'observaciones' =>
                $validated['observaciones'] ?? null,
            ]);
        });

        $llamada->load([
            'departamento',
            'usuario.role',
        ]);

        return response()->json([
            'success' => true,

            'message' =>
            'Llamada registrada correctamente.',

            'data' => $llamada,
        ], 201);
    }

    /**
     * Actualizar información de una llamada.
     */
    public function update(
        Request $request,
        Llamada $llamada
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Verificar permisos
        |--------------------------------------------------------------------------
        */

        if (!$this->puedeAcceder($llamada)) {

            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar esta llamada.',
            ], 403);
        }

        $validated = $request->validate([

            'telefono' => [
                'required',
                'string',
                'max:20',
            ],

            'nombre' => [
                'required',
                'string',
                'max:150',
            ],

            'motivo' => [
                'required',
                'string',
            ],

            'categoria' => [
                'required',
                'string',
                'max:50',
            ],

            'departamento_id' => [
                'required',
                'exists:departamentos,id',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],
        ]);

        $llamada->update($validated);

        $llamada->load([
            'departamento',
            'usuario.role',
        ]);

        return response()->json([
            'success' => true,

            'message' =>
            'Llamada actualizada correctamente.',

            'data' => $llamada,
        ]);
    }

    /**
     * Cambiar estado de una llamada.
     */
    public function toggleStatus(
        Request $request,
        Llamada $llamada
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Verificar permisos
        |--------------------------------------------------------------------------
        */

        if (!$this->puedeAcceder($llamada)) {

            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar el estado de esta llamada.',
            ], 403);
        }

        $validated = $request->validate([

            'estado' => [
                'required',
                'in:en_proceso,transferida,finalizada',
            ],
        ]);

        $estado = $validated['estado'];

        /*
        |--------------------------------------------------------------------------
        | Actualizar fechas dependiendo del estado
        |--------------------------------------------------------------------------
        */

        $datos = [
            'estado' => $estado,
        ];

        if ($estado === 'transferida') {

            $datos['transferida_at'] = now();

            $datos['finalizada_at'] = null;
        }

        if ($estado === 'finalizada') {

            $datos['finalizada_at'] = now();
        }

        if ($estado === 'en_proceso') {

            $datos['transferida_at'] = null;

            $datos['finalizada_at'] = null;
        }

        $llamada->update($datos);

        $llamada->load([
            'departamento',
            'usuario.role',
        ]);

        return response()->json([
            'success' => true,

            'message' =>
            'Estado actualizado correctamente.',

            'data' => $llamada,
        ]);
    }

    /**
     * Determina si el usuario puede acceder a una llamada.
     */
    private function puedeAcceder(Llamada $llamada): bool
    {
        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Super Administrador y Supervisor
        |--------------------------------------------------------------------------
        |
        | Pueden acceder a cualquier llamada.
        |
        */

        if (
            $usuario->esSuperAdmin() ||
            $usuario->esSupervisor()
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Jefe de departamento
        |--------------------------------------------------------------------------
        |
        | Solo puede acceder a llamadas de su departamento.
        |
        */

        if ($usuario->esJefeDepartamento()) {

            return (int) $llamada->departamento_id
                === (int) $usuario->departamento_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Recepcionista
        |--------------------------------------------------------------------------
        |
        | Solo puede acceder a llamadas que él mismo registró.
        |
        */

        if ($usuario->esRecepcionista()) {

            return (int) $llamada->usuario_id
                === (int) $usuario->id;
        }

        return false;
    }

    /**
     * Generar folio automático.
     */
    private function generarFolio(): string
    {
        $fecha = now()->format('Ymd');

        $ultimoFolio = Llamada::whereDate(
            'fecha',
            now()->toDateString()
        )
            ->orderByDesc('id')
            ->value('folio');

        $numero = 1;

        if ($ultimoFolio) {

            $ultimoNumero = (int) substr(
                $ultimoFolio,
                -4
            );

            $numero = $ultimoNumero + 1;
        }

        return 'CALL-' . $fecha . '-' .
            str_pad(
                $numero,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}
