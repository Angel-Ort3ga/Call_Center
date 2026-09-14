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
     * Aplica el período seleccionado a una consulta.
     *
     * Períodos disponibles:
     *
     * hoy
     * semana
     * mes
     * todo
     * personalizado
     */
    private function aplicarPeriodo($query, Request $request)
    {
        $periodo = $request->input('periodo', 'hoy');

        /*
        |--------------------------------------------------------------------------
        | Personalizado
        |--------------------------------------------------------------------------
        */

        if ($periodo === 'personalizado') {

            $fechaDesde = $request->input('fecha_desde');
            $fechaHasta = $request->input('fecha_hasta');

            if ($fechaDesde && $fechaHasta) {
                $query->whereBetween('fecha', [
                    $fechaDesde,
                    $fechaHasta
                ]);
            } elseif ($fechaDesde) {
                $query->whereDate('fecha', '>=', $fechaDesde);
            } elseif ($fechaHasta) {
                $query->whereDate('fecha', '<=', $fechaHasta);
            }

            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Hoy
        |--------------------------------------------------------------------------
        */

        if ($periodo === 'hoy') {

            $query->whereDate(
                'fecha',
                now()->toDateString()
            );

            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Semana actual
        |--------------------------------------------------------------------------
        |
        | Desde el lunes hasta hoy.
        |
        */

        if ($periodo === 'semana') {

            $inicioSemana = now()
                ->startOfWeek()
                ->toDateString();

            $hoy = now()
                ->toDateString();

            $query->whereBetween('fecha', [
                $inicioSemana,
                $hoy
            ]);

            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Mes actual
        |--------------------------------------------------------------------------
        |
        | Desde el primer día del mes hasta hoy.
        |
        */

        if ($periodo === 'mes') {

            $inicioMes = now()
                ->startOfMonth()
                ->toDateString();

            $hoy = now()
                ->toDateString();

            $query->whereBetween('fecha', [
                $inicioMes,
                $hoy
            ]);

            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Todo
        |--------------------------------------------------------------------------
        |
        | No agregamos filtro de fecha.
        |
        */

        if ($periodo === 'todo') {
            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Compatibilidad con el filtro antiguo "fecha"
        |--------------------------------------------------------------------------
        |
        | Si algún componente todavía manda:
        |
        | fecha=2026-09-08
        |
        | seguimos soportándolo.
        |
        */

        if ($request->filled('fecha')) {

            $query->whereDate(
                'fecha',
                $request->fecha
            );

            return $query;
        }

        /*
        |--------------------------------------------------------------------------
        | Valor por defecto
        |--------------------------------------------------------------------------
        |
        | Si no se especificó ningún período, mostramos hoy.
        |
        */

        $query->whereDate(
            'fecha',
            now()->toDateString()
        );

        return $query;
    }


    /**
     * Listar llamadas.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->consultaPermitida()
            ->with([
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

                $q->where(
                    'folio',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'nombre',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'telefono',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'categoria',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'motivo',
                        'like',
                        "%{$search}%"
                    );
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
        | Filtrar por período
        |--------------------------------------------------------------------------
        |
        | Aquí estaba una de las causas del problema.
        |
        | Ahora la tabla también entiende:
        |
        | hoy
        | semana
        | mes
        | todo
        | personalizado
        |
        */

        $this->aplicarPeriodo(
            $query,
            $request
        );

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
        | Período
        |--------------------------------------------------------------------------
        */

        $periodo = $request->input(
            'periodo',
            'hoy'
        );

        /*
        |--------------------------------------------------------------------------
        | Consulta base
        |--------------------------------------------------------------------------
        */

        $query = $this->consultaPermitida();

        /*
        |--------------------------------------------------------------------------
        | Aplicar período
        |--------------------------------------------------------------------------
        */

        $this->aplicarPeriodo(
            $query,
            $request
        );

        /*
        |--------------------------------------------------------------------------
        | Resumen por estado
        |--------------------------------------------------------------------------
        */

        $total = (clone $query)->count();

        $enProceso = (clone $query)
            ->where(
                'estado',
                'en_proceso'
            )
            ->count();

        $transferidas = (clone $query)
            ->where(
                'estado',
                'transferida'
            )
            ->count();

        $finalizadas = (clone $query)
            ->where(
                'estado',
                'finalizada'
            )
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
                DB::raw(
                    'COUNT(llamadas.id) as total'
                )
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
                DB::raw(
                    'COUNT(*) as total'
                )
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
                DB::raw(
                    'COUNT(*) as total'
                )
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
                    'HOUR(hora) as hora'
                ),
                DB::raw(
                    'COUNT(*) as total'
                )
            )
            ->groupBy(
                DB::raw(
                    'HOUR(hora)'
                )
            )
            ->orderBy('hora')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Información del período
        |--------------------------------------------------------------------------
        */

        $fechaDesde = null;
        $fechaHasta = null;

        if ($periodo === 'hoy') {

            $fechaDesde = now()->toDateString();
            $fechaHasta = now()->toDateString();
        } elseif ($periodo === 'semana') {

            $fechaDesde = now()
                ->startOfWeek()
                ->toDateString();

            $fechaHasta = now()
                ->toDateString();
        } elseif ($periodo === 'mes') {

            $fechaDesde = now()
                ->startOfMonth()
                ->toDateString();

            $fechaHasta = now()
                ->toDateString();
        } elseif ($periodo === 'personalizado') {

            $fechaDesde = $request->input(
                'fecha_desde'
            );

            $fechaHasta = $request->input(
                'fecha_hasta'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Respuesta
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,

            'data' => [

                'periodo' => $periodo,

                'fecha' => $fechaHasta,

                'fecha_desde' => $fechaDesde,

                'fecha_hasta' => $fechaHasta,

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
                'message' =>
                'No tienes permiso para acceder a esta llamada.',
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

        $llamada = DB::transaction(
            function () use (
                $validated,
                $usuario
            ) {

                $ahora = now();

                $folio = $this->generarFolio();

                return Llamada::create([

                    'folio' => $folio,

                    'fecha' => $ahora->toDateString(),

                    'hora' => $ahora->format('H:i:s'),

                    'telefono' =>
                    $validated['telefono'],

                    'nombre' =>
                    $validated['nombre'],

                    'motivo' =>
                    $validated['motivo'],

                    'categoria' =>
                    $validated['categoria'],

                    'departamento_id' =>
                    $validated['departamento_id'],

                    'usuario_id' =>
                    $usuario->id,

                    'estado' =>
                    'en_proceso',

                    'observaciones' =>
                    $validated['observaciones'] ?? null,
                ]);
            }
        );

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
                'message' =>
                'No tienes permiso para modificar esta llamada.',
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
     *
     * Flujo normal:
     *
     * Recepcionista:
     *      en_proceso -> transferida
     *
     * Jefe de departamento:
     *      transferida -> finalizada
     *
     * Supervisor / Super Admin:
     *      pueden administrar cualquier transición permitida.
     */
    public function toggleStatus(
        Request $request,
        Llamada $llamada
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Verificar acceso a la llamada
        |--------------------------------------------------------------------------
        */

        if (!$this->puedeAcceder($llamada)) {

            return response()->json([
                'success' => false,
                'message' =>
                'No tienes permiso para modificar esta llamada.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar nuevo estado
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'estado' => [
                'required',
                'in:en_proceso,transferida,finalizada',
            ],

        ]);

        $nuevoEstado = $validated['estado'];

        $estadoActual = $llamada->estado;

        $usuario = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Super Admin / Supervisor
        |--------------------------------------------------------------------------
        |
        | Pueden administrar los estados.
        |
        */

        if (
            $usuario->esSuperAdmin() ||
            $usuario->esSupervisor()
        ) {

            $this->actualizarEstado(
                $llamada,
                $nuevoEstado
            );

            return response()->json([

                'success' => true,

                'message' =>
                'Estado actualizado correctamente.',

                'data' => $llamada->fresh([
                    'departamento',
                    'usuario.role',
                ]),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RECEPCIONISTA
        |--------------------------------------------------------------------------
        |
        | Solamente puede transferir una llamada que está
        | actualmente en proceso.
        |
        */

        if ($usuario->esRecepcionista()) {

            if (
                $estadoActual !== 'en_proceso' ||
                $nuevoEstado !== 'transferida'
            ) {

                return response()->json([

                    'success' => false,

                    'message' =>
                    'La recepcionista solamente puede transferir llamadas que están en proceso.',

                ], 403);
            }

            $this->actualizarEstado(
                $llamada,
                $nuevoEstado
            );

            return response()->json([

                'success' => true,

                'message' =>
                'Llamada transferida correctamente.',

                'data' => $llamada->fresh([
                    'departamento',
                    'usuario.role',
                ]),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | JEFE DE DEPARTAMENTO
        |--------------------------------------------------------------------------
        |
        | Solamente puede finalizar llamadas:
        |
        | transferida -> finalizada
        |
        | Además, puedeAcceder() ya garantiza que la llamada
        | pertenezca a su departamento.
        |
        */

        if ($usuario->esJefeDepartamento()) {

            if (
                $estadoActual !== 'transferida' ||
                $nuevoEstado !== 'finalizada'
            ) {

                return response()->json([

                    'success' => false,

                    'message' =>
                    'El jefe de departamento solamente puede finalizar llamadas transferidas.',

                ], 403);
            }

            $this->actualizarEstado(
                $llamada,
                $nuevoEstado
            );

            return response()->json([

                'success' => true,

                'message' =>
                'Llamada finalizada correctamente.',

                'data' => $llamada->fresh([
                    'departamento',
                    'usuario.role',
                ]),

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Usuario sin permisos
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => false,

            'message' =>
            'No tienes permisos para cambiar el estado de esta llamada.',

        ], 403);
    }


    /**
     * Actualizar estado y fechas relacionadas.
     */
    private function actualizarEstado(
        Llamada $llamada,
        string $estado
    ): void {

        $datos = [
            'estado' => $estado,
        ];

        /*
        |--------------------------------------------------------------------------
        | Transferida
        |--------------------------------------------------------------------------
        */

        if ($estado === 'transferida') {

            $datos['transferida_at'] = now();

            $datos['finalizada_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Finalizada
        |--------------------------------------------------------------------------
        */

        if ($estado === 'finalizada') {

            /*
            | Si por alguna razón no existe fecha de transferencia,
            | no la inventamos.
            |
            | La llamada normalmente llegará aquí después de
            | estar en estado "transferida".
            */

            $datos['finalizada_at'] = now();
        }

        /*
        |--------------------------------------------------------------------------
        | En proceso
        |--------------------------------------------------------------------------
        */

        if ($estado === 'en_proceso') {

            $datos['transferida_at'] = null;

            $datos['finalizada_at'] = null;
        }

        $llamada->update($datos);
    }


    /**
     * Determina si el usuario puede acceder a una llamada.
     */
    private function puedeAcceder(
        Llamada $llamada
    ): bool {

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
