```vue
<template>

    <div class="departamentos-page">

        <!-- ============================================================ -->
        <!-- HEADER -->
        <!-- ============================================================ -->

        <DepartamentoHeader
            :departamento="departamentoSeleccionado"
            :cargando="
                departamentoSeleccionado
                    ? cargandoDetalle
                    : cargando
            "
            @actualizar="
                departamentoSeleccionado
                    ? cargarLlamadasDepartamento()
                    : cargarResumen()
            "
            @volver="cerrarDepartamento"
        />


        <!-- ============================================================ -->
        <!-- VISTA GENERAL -->
        <!-- ============================================================ -->

        <template v-if="!departamentoSeleccionado">

            <!-- ERROR -->

            <div
                v-if="error"
                class="error-message"
            >

                <i class="fas fa-exclamation-circle"></i>

                {{ error }}

            </div>


            <!-- ======================================================== -->
            <!-- FILTROS DE PERIODO -->
            <!-- ======================================================== -->

            <div class="content-card period-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Periodo
                        </h2>

                        <p>
                            Selecciona el periodo que deseas consultar.
                        </p>

                    </div>

                </div>


                <div class="period-filters">

                    <div class="period-buttons">

                        <button
                            type="button"
                            class="period-button"
                            :class="{
                                active: periodo === 'hoy'
                            }"
                            @click="seleccionarPeriodo('hoy')"
                        >

                            <i class="fas fa-calendar-day"></i>

                            Hoy

                        </button>


                        <button
                            type="button"
                            class="period-button"
                            :class="{
                                active: periodo === 'semana'
                            }"
                            @click="seleccionarPeriodo('semana')"
                        >

                            <i class="fas fa-calendar-week"></i>

                            Esta semana

                        </button>


                        <button
                            type="button"
                            class="period-button"
                            :class="{
                                active: periodo === 'mes'
                            }"
                            @click="seleccionarPeriodo('mes')"
                        >

                            <i class="fas fa-calendar-alt"></i>

                            Este mes

                        </button>


                        <button
                            type="button"
                            class="period-button"
                            :class="{
                                active: periodo === 'todo'
                            }"
                            @click="seleccionarPeriodo('todo')"
                        >

                            <i class="fas fa-calendar"></i>

                            Todo

                        </button>


                        <button
                            type="button"
                            class="period-button"
                            :class="{
                                active: periodo === 'personalizado'
                            }"
                            @click="seleccionarPeriodo('personalizado')"
                        >

                            <i class="fas fa-calendar-plus"></i>

                            Personalizado

                        </button>

                    </div>


                    <!-- FECHAS PERSONALIZADAS -->

                    <div
                        v-if="periodo === 'personalizado'"
                        class="custom-date-filters"
                    >

                        <div class="date-filter-item">

                            <label>
                                Desde
                            </label>

                            <input
                                v-model="fechaDesde"
                                type="date"
                                class="date-input"
                            >

                        </div>


                        <div class="date-filter-item">

                            <label>
                                Hasta
                            </label>

                            <input
                                v-model="fechaHasta"
                                type="date"
                                class="date-input"
                            >

                        </div>


                        <button
                            type="button"
                            class="search-button"
                            :disabled="cargando"
                            @click="aplicarPeriodo"
                        >

                            <i class="fas fa-filter"></i>

                            Aplicar

                        </button>

                    </div>

                </div>

            </div>


            <!-- ======================================================== -->
            <!-- TARJETAS + CATEGORÍAS + MOTIVOS + HORAS -->
            <!-- ======================================================== -->

            <DepartamentoStatsCards
                :resumen="resumen"
                :por-categoria="porCategoria"
                :motivos="motivos"
                :por-hora="porHora"
            />


            <!-- ======================================================== -->
            <!-- DEPARTAMENTOS -->
            <!-- ======================================================== -->

            <DepartamentoTable
                :departamentos="porDepartamento"
                :resumen-total="Number(resumen.total) || 0"
                @seleccionar="abrirDepartamento"
            />

        </template>


        <!-- ============================================================ -->
        <!-- VISTA DETALLE DEL DEPARTAMENTO -->
        <!-- ============================================================ -->

        <template v-else>

            <DepartamentoModal
                :departamento="departamentoSeleccionado"
                :busqueda="detalleBusqueda"
                :estado="detalleEstado"
                :periodo="detallePeriodo"
                :fecha-desde="detalleFechaDesde"
                :fecha-hasta="detalleFechaHasta"
                :cargando="cargandoDetalle"
                @buscar="buscarDetalle"
                @cambio-periodo="cambioPeriodoDetalle"
            />


            <DepartamentoTable
                :departamentos="[]"
                :resumen-total="0"
                :departamento-seleccionado="departamentoSeleccionado"
                :llamadas="llamadasDepartamento"
                :cargando="cargandoDetalle"
                :total="llamadasDepartamentoTotal"
                :pagina="detallePagina"
                :total-paginas="detalleTotalPaginas"
                @pagina="cambiarPaginaDetalle"
            />

        </template>

    </div>

</template>


<script>

import axios from 'axios';

import DepartamentoHeader
    from './components/DepartamentosHeader.vue';

import DepartamentoStatsCards
    from './components/DepartamentoCards.vue';

import DepartamentoTable
    from './components/DepartamentoTable.vue';

import DepartamentoModal
    from './components/DepartamentoModal.vue';

import './styles/departamentos.css'

export default {

    name: 'DepartamentosIndex',


    components: {

        DepartamentoHeader,

        DepartamentoStatsCards,

        DepartamentoTable,

        DepartamentoModal,

    },


    data() {

        return {

            /*
            |------------------------------------------------------------------
            | FILTROS DEL RESUMEN
            |------------------------------------------------------------------
            */

            periodo: 'hoy',

            fechaDesde: '',

            fechaHasta: '',


            /*
            |------------------------------------------------------------------
            | ESTADO GENERAL
            |------------------------------------------------------------------
            */

            cargando: false,

            error: null,


            /*
            |------------------------------------------------------------------
            | RESUMEN
            |------------------------------------------------------------------
            */

            resumen: {

                total: 0,

                en_proceso: 0,

                transferidas: 0,

                finalizadas: 0,

            },

            porDepartamento: [],

            porCategoria: [],

            motivos: [],

            porHora: [],


            /*
            |------------------------------------------------------------------
            | DEPARTAMENTO SELECCIONADO
            |------------------------------------------------------------------
            */

            departamentoSeleccionado: null,

            llamadasDepartamento: [],

            cargandoDetalle: false,

            detalleBusqueda: '',

            detalleEstado: '',

            detallePeriodo: 'hoy',

            detalleFechaDesde: '',

            detalleFechaHasta: '',

            detallePagina: 1,

            detalleTotalPaginas: 1,

            llamadasDepartamentoTotal: 0,

        };

    },


    mounted() {

        this.cargarResumen();

    },


    methods: {

        /*
        |==================================================================
        | RESUMEN GENERAL
        |==================================================================
        */

        async cargarResumen() {

            this.error = null;


            if (
                this.periodo === 'personalizado' &&
                (
                    !this.fechaDesde ||
                    !this.fechaHasta
                )
            ) {

                this.error =
                    'Selecciona una fecha inicial y una fecha final.';

                return;

            }


            if (
                this.periodo === 'personalizado' &&
                this.fechaDesde > this.fechaHasta
            ) {

                this.error =
                    'La fecha inicial no puede ser posterior a la fecha final.';

                return;

            }


            this.cargando = true;


            try {

                const params = {

                    periodo: this.periodo,

                };


                if (
                    this.periodo === 'personalizado'
                ) {

                    params.fecha_desde =
                        this.fechaDesde;

                    params.fecha_hasta =
                        this.fechaHasta;

                }


                const response =
                    await axios.get(
                        '/api/admin/llamadas/resumen',
                        {
                            params,
                        }
                    );


                console.log(
                    'Respuesta resumen:',
                    response.data
                );


                const data =
                    response.data &&
                    response.data.data
                        ? response.data.data
                        : {};


                this.resumen =
                    data.resumen || {

                        total: 0,

                        en_proceso: 0,

                        transferidas: 0,

                        finalizadas: 0,

                    };


                this.porDepartamento =
                    Array.isArray(data.por_departamento)
                        ? data.por_departamento
                        : [];


                this.porCategoria =
                    Array.isArray(data.por_categoria)
                        ? data.por_categoria
                        : [];


                this.motivos =
                    Array.isArray(data.motivos)
                        ? data.motivos
                        : [];


                this.porHora =
                    Array.isArray(data.por_hora)
                        ? data.por_hora
                        : [];


            } catch (error) {

                console.error(
                    'Error al cargar resumen:',
                    error
                );


                this.limpiarResumen();


                if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                } else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar el resumen.';

                } else if (
                    error.response &&
                    error.response.status === 422
                ) {

                    this.error =
                        'Los datos enviados para el filtro no son válidos.';

                } else {

                    this.error =
                        'No fue posible cargar el resumen de llamadas.';

                }

            } finally {

                this.cargando = false;

            }

        },


        /*
        |==================================================================
        | LIMPIAR RESUMEN
        |==================================================================
        */

        limpiarResumen() {

            this.resumen = {

                total: 0,

                en_proceso: 0,

                transferidas: 0,

                finalizadas: 0,

            };

            this.porDepartamento = [];

            this.porCategoria = [];

            this.motivos = [];

            this.porHora = [];

        },


        /*
        |==================================================================
        | PERIODOS
        |==================================================================
        */

        seleccionarPeriodo(periodo) {

            this.periodo =
                periodo;

            this.error = null;


            if (
                periodo === 'personalizado'
            ) {

                return;

            }


            this.cargarResumen();

        },


        aplicarPeriodo() {

            this.error = null;


            if (
                !this.fechaDesde ||
                !this.fechaHasta
            ) {

                this.error =
                    'Selecciona una fecha inicial y una fecha final.';

                return;

            }


            if (
                this.fechaDesde >
                this.fechaHasta
            ) {

                this.error =
                    'La fecha inicial no puede ser posterior a la fecha final.';

                return;

            }


            this.cargarResumen();

        },


        /*
        |==================================================================
        | ABRIR DEPARTAMENTO
        |==================================================================
        */

        abrirDepartamento(departamento) {

            this.departamentoSeleccionado =
                departamento;


            this.detalleBusqueda =
                '';

            this.detalleEstado =
                '';


            this.detallePeriodo =
                this.periodo;


            this.detalleFechaDesde =
                this.fechaDesde;


            this.detalleFechaHasta =
                this.fechaHasta;


            this.detallePagina =
                1;


            this.detalleTotalPaginas =
                1;


            this.llamadasDepartamento =
                [];


            this.llamadasDepartamentoTotal =
                0;


            this.cargarLlamadasDepartamento();

        },


        /*
        |==================================================================
        | CERRAR DEPARTAMENTO
        |==================================================================
        */

        cerrarDepartamento() {

            this.departamentoSeleccionado =
                null;

            this.llamadasDepartamento =
                [];

            this.llamadasDepartamentoTotal =
                0;

            this.detalleBusqueda =
                '';

            this.detalleEstado =
                '';

            this.detallePeriodo =
                'hoy';

            this.detalleFechaDesde =
                '';

            this.detalleFechaHasta =
                '';

            this.detallePagina =
                1;

            this.detalleTotalPaginas =
                1;

        },


        /*
        |==================================================================
        | FILTROS DEL DETALLE
        |==================================================================
        */

        buscarDetalle(filtros) {

            if (!filtros) {
                return;
            }


            this.detalleBusqueda =
                filtros.busqueda || '';

            this.detalleEstado =
                filtros.estado || '';

            this.detallePeriodo =
                filtros.periodo || 'hoy';

            this.detalleFechaDesde =
                filtros.fechaDesde || '';

            this.detalleFechaHasta =
                filtros.fechaHasta || '';


            this.detallePagina =
                1;


            this.cargarLlamadasDepartamento();

        },


        cambioPeriodoDetalle(periodo) {

            this.detallePeriodo =
                periodo;


            if (
                periodo === 'personalizado'
            ) {

                return;

            }


            this.detallePagina =
                1;


            this.cargarLlamadasDepartamento();

        },


        /*
        |==================================================================
        | PAGINACIÓN
        |==================================================================
        */

        cambiarPaginaDetalle(pagina) {

            if (
                pagina < 1 ||
                pagina > this.detalleTotalPaginas
            ) {

                return;

            }


            this.detallePagina =
                pagina;


            this.cargarLlamadasDepartamento();

        },


        /*
        |==================================================================
        | LLAMADAS DEL DEPARTAMENTO
        |==================================================================
        */

        async cargarLlamadasDepartamento() {

            if (
                !this.departamentoSeleccionado
            ) {

                return;

            }


            if (
                this.detallePeriodo === 'personalizado' &&
                (
                    !this.detalleFechaDesde ||
                    !this.detalleFechaHasta
                )
            ) {

                return;

            }


            if (
                this.detallePeriodo === 'personalizado' &&
                this.detalleFechaDesde >
                this.detalleFechaHasta
            ) {

                return;

            }


            this.cargandoDetalle = true;


            try {

                const params = {

                    search:
                        this.detalleBusqueda || undefined,

                    estado:
                        this.detalleEstado || undefined,

                    periodo:
                        this.detallePeriodo,

                    page:
                        this.detallePagina,

                };


                if (
                    this.detallePeriodo ===
                    'personalizado'
                ) {

                    params.fecha_desde =
                        this.detalleFechaDesde;

                    params.fecha_hasta =
                        this.detalleFechaHasta;

                }


                console.log(
                    'Parámetros detalle:',
                    params
                );


                const response =
                    await axios.get(
                        `/api/admin/departamentos/${this.departamentoSeleccionado.id}/llamadas`,
                        {
                            params,
                        }
                    );


                console.log(
                    'Respuesta llamadas departamento:',
                    response.data
                );


                const data =
                    response.data &&
                    response.data.data
                        ? response.data.data
                        : {};


                let paginacion =
                    data.llamadas || data;


                if (
                    Array.isArray(paginacion)
                ) {

                    this.llamadasDepartamento =
                        paginacion;

                    this.detalleTotalPaginas =
                        1;

                    this.llamadasDepartamentoTotal =
                        paginacion.length;

                } else {

                    this.llamadasDepartamento =
                        Array.isArray(
                            paginacion.data
                        )
                            ? paginacion.data
                            : [];


                    this.detallePagina =
                        Number(
                            paginacion.current_page ||
                            this.detallePagina ||
                            1
                        );


                    this.detalleTotalPaginas =
                        Number(
                            paginacion.last_page ||
                            1
                        );


                    this.llamadasDepartamentoTotal =
                        Number(
                            paginacion.total ||
                            this.llamadasDepartamento.length
                        );

                }


            } catch (error) {

                console.error(
                    'Error al cargar llamadas del departamento:',
                    error
                );


                this.llamadasDepartamento =
                    [];

                this.llamadasDepartamentoTotal =
                    0;

                this.detalleTotalPaginas =
                    1;

            } finally {

                this.cargandoDetalle =
                    false;

            }

        },

    },

};

</script>


<!--
    IMPORTANTE:
    Este style NO lleva "scoped".

    Los componentes hijos utilizan las mismas clases CSS que
    originalmente estaban en este Index.vue. Al dejarlo global
    dentro del módulo conservamos exactamente la apariencia.
-->

<style>

.departamentos-page {

    min-height: 100vh;

    padding: 30px;

    background: #f5f7fa;

}


/* ====================================================================== */
/* HEADER                                                                */
/* ====================================================================== */

.page-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 30px;

}


.page-title {

    margin: 0;

    color: #1f2937;

    font-size: 28px;

    font-weight: 700;

}


.page-subtitle {

    margin: 6px 0 0;

    color: #6b7280;

    font-size: 14px;

}


.header-actions {

    display: flex;

    align-items: center;

    gap: 10px;

}


.refresh-button {

    height: 40px;

    padding: 0 16px;

    border: none;

    border-radius: 7px;

    background: #2563eb;

    color: #ffffff;

    cursor: pointer;

    font-weight: 600;

}


.refresh-button:hover {

    background: #1d4ed8;

}


.refresh-button:disabled {

    opacity: 0.6;

    cursor: not-allowed;

}


/* ====================================================================== */
/* BOTÓN REGRESAR                                                        */
/* ====================================================================== */

.back-button {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 12px;

    padding: 0;

    border: none;

    background: transparent;

    color: #2563eb;

    cursor: pointer;

    font-size: 14px;

    font-weight: 600;

}


.back-button:hover {

    color: #1d4ed8;

}


/* ====================================================================== */
/* ERROR                                                                 */
/* ====================================================================== */

.error-message {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 20px;

    padding: 14px 16px;

    border-radius: 8px;

    background: #fee2e2;

    color: #991b1b;

    font-size: 14px;

}


/* ====================================================================== */
/* CARDS                                                                 */
/* ====================================================================== */

.content-card {

    margin-bottom: 25px;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    background: #ffffff;

    overflow: hidden;

}


.card-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 22px 24px;

    border-bottom: 1px solid #e5e7eb;

}


.card-header h2 {

    margin: 0;

    color: #1f2937;

    font-size: 18px;

}


.card-header p {

    margin: 5px 0 0;

    color: #6b7280;

    font-size: 13px;

}


.card-hint {

    color: #9ca3af;

    font-size: 12px;

}


.calls-count {

    padding: 6px 10px;

    border-radius: 6px;

    background: #eff6ff;

    color: #2563eb;

    font-size: 12px;

    font-weight: 600;

}


/* ====================================================================== */
/* FILTROS DE PERIODO                                                   */
/* ====================================================================== */

.period-card {

    margin-bottom: 25px;

}


.period-filters {

    padding: 20px 24px;

}


.period-buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

}


.period-button {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    height: 40px;

    padding: 0 15px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #4b5563;

    cursor: pointer;

    font-size: 13px;

    font-weight: 600;

}


.period-button:hover {

    border-color: #2563eb;

    color: #2563eb;

}


.period-button.active {

    border-color: #2563eb;

    background: #2563eb;

    color: #ffffff;

}


/* ====================================================================== */
/* FECHAS                                                                */
/* ====================================================================== */

.custom-date-filters {

    display: flex;

    align-items: flex-end;

    gap: 12px;

    margin-top: 18px;

    padding-top: 18px;

    border-top: 1px solid #e5e7eb;

}


.date-filter-item {

    display: flex;

    flex-direction: column;

    gap: 6px;

}


.date-filter-item label {

    color: #6b7280;

    font-size: 12px;

    font-weight: 600;

}


.date-input {

    height: 40px;

    padding: 0 12px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    outline: none;

}


.date-input:focus {

    border-color: #2563eb;

}


/* ====================================================================== */
/* ESTADÍSTICAS                                                          */
/* ====================================================================== */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 30px;

}


.stat-card {

    display: flex;

    align-items: center;

    gap: 16px;

    padding: 22px;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    background: #ffffff;

}


.stat-icon {

    width: 48px;

    height: 48px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    font-size: 18px;

}


.stat-icon.total {

    background: #e0f2fe;

    color: #0284c7;

}


.stat-icon.process {

    background: #fef3c7;

    color: #d97706;

}


.stat-icon.transferred {

    background: #ede9fe;

    color: #7c3aed;

}


.stat-icon.finished {

    background: #dcfce7;

    color: #16a34a;

}


.stat-label {

    display: block;

    margin-bottom: 5px;

    color: #6b7280;

    font-size: 13px;

}


.stat-value {

    display: block;

    color: #111827;

    font-size: 26px;

}


/* ====================================================================== */
/* DEPARTAMENTOS                                                         */
/* ====================================================================== */

.departments-list {

    padding: 10px 24px 20px;

}


.department-row {

    display: grid;

    grid-template-columns:
        260px
        1fr
        70px;

    align-items: center;

    gap: 20px;

    padding: 15px 0;

    border-bottom: 1px solid #f0f0f0;

    cursor: pointer;

    transition:
        background 0.2s ease,
        padding 0.2s ease;

}


.department-row:last-child {

    border-bottom: none;

}


.department-row:hover {

    margin: 0 -12px;

    padding-left: 12px;

    padding-right: 12px;

    border-radius: 8px;

    background: #f9fafb;

}


.department-info {

    display: flex;

    align-items: center;

    gap: 12px;

}


.department-icon {

    width: 38px;

    height: 38px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    background: #eff6ff;

    color: #2563eb;

}


.department-info strong {

    display: block;

    color: #374151;

    font-size: 14px;

}


.department-info span {

    display: block;

    margin-top: 3px;

    color: #9ca3af;

    font-size: 12px;

}


.progress-background {

    width: 100%;

    height: 9px;

    overflow: hidden;

    border-radius: 999px;

    background: #e5e7eb;

}


.progress-bar {

    height: 100%;

    border-radius: 999px;

    background: #2563eb;

    transition: width 0.3s ease;

}


.department-action {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 10px;

}


.department-action strong {

    color: #374151;

}


.department-action i {

    color: #9ca3af;

    font-size: 12px;

}


/* ====================================================================== */
/* DETALLE                                                               */
/* ====================================================================== */

.department-detail-card {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

    margin-bottom: 25px;

    padding: 22px;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    background: #ffffff;

}


.detail-item span {

    display: block;

    margin-bottom: 6px;

    color: #9ca3af;

    font-size: 12px;

}


.detail-item strong {

    color: #374151;

    font-size: 14px;

}


/* ====================================================================== */
/* FILTROS DETALLE                                                       */
/* ====================================================================== */

.detail-filters {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 20px 24px;

}


.search-input {

    flex: 1;

    height: 40px;

    min-width: 200px;

    padding: 0 12px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    outline: none;

}


.search-input:focus {

    border-color: #2563eb;

}


.filter-select {

    height: 40px;

    padding: 0 12px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    outline: none;

}


.filter-select:focus {

    border-color: #2563eb;

}


.search-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    height: 40px;

    padding: 0 16px;

    border: none;

    border-radius: 7px;

    background: #2563eb;

    color: #ffffff;

    cursor: pointer;

    font-weight: 600;

    white-space: nowrap;

}


.search-button:hover {

    background: #1d4ed8;

}


.search-button:disabled {

    opacity: 0.6;

    cursor: not-allowed;

}


/* ====================================================================== */
/* TABLA                                                                 */
/* ====================================================================== */

.table-container {

    width: 100%;

    overflow-x: auto;

}


.calls-table {

    width: 100%;

    border-collapse: collapse;

}


.calls-table th {

    padding: 14px 20px;

    background: #f9fafb;

    color: #6b7280;

    text-align: left;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    white-space: nowrap;

}


.calls-table td {

    padding: 15px 20px;

    border-top: 1px solid #f0f0f0;

    color: #374151;

    font-size: 13px;

    white-space: nowrap;

}


.calls-table tbody tr:hover {

    background: #fafafa;

}


.calls-table td strong {

    color: #2563eb;

}


/* ====================================================================== */
/* PAGINACIÓN                                                            */
/* ====================================================================== */

.pagination {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 15px;

    padding: 18px 24px;

    border-top: 1px solid #e5e7eb;

}


.pagination-button {

    width: 36px;

    height: 36px;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    cursor: pointer;

}


.pagination-button:hover:not(:disabled) {

    border-color: #2563eb;

    color: #2563eb;

}


.pagination-button:disabled {

    opacity: 0.45;

    cursor: not-allowed;

}


.pagination-info {

    color: #6b7280;

    font-size: 13px;

}


/* ====================================================================== */
/* ESTADOS                                                               */
/* ====================================================================== */

.status-badge {

    display: inline-flex;

    padding: 5px 9px;

    border-radius: 999px;

    font-size: 11px;

    font-weight: 600;

}


.status-active {

    background: #dcfce7;

    color: #166534;

}


.status-inactive {

    background: #fee2e2;

    color: #991b1b;

}


.status-process {

    background: #fef3c7;

    color: #92400e;

}


.status-transferred {

    background: #ede9fe;

    color: #6d28d9;

}


.status-finished {

    background: #dcfce7;

    color: #166534;

}


/* ====================================================================== */
/* CATEGORÍAS                                                            */
/* ====================================================================== */

.two-columns {

    display: grid;

    grid-template-columns:
        1fr
        1fr;

    gap: 25px;

}


.two-columns .content-card {

    margin-bottom: 0;

}


.category-list {

    padding: 15px 24px 20px;

}


.category-row {

    display: grid;

    grid-template-columns:
        120px
        1fr
        45px;

    align-items: center;

    gap: 15px;

    padding: 12px 0;

}


.category-name {

    color: #374151;

    font-size: 14px;

    font-weight: 600;

}


.category-bar-background {

    width: 100%;

    height: 8px;

    overflow: hidden;

    border-radius: 999px;

    background: #e5e7eb;

}


.category-bar {

    height: 100%;

    border-radius: 999px;

    background: #7c3aed;

}


.category-row strong {

    color: #374151;

    text-align: right;

}


/* ====================================================================== */
/* MOTIVOS                                                               */
/* ====================================================================== */

.motivos-list {

    padding: 10px 24px 20px;

}


.motivo-row {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 13px 0;

    border-bottom: 1px solid #f0f0f0;

}


.motivo-row:last-child {

    border-bottom: none;

}


.motivo-position {

    width: 28px;

    height: 28px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #f3f4f6;

    color: #6b7280;

    font-size: 12px;

    font-weight: 700;

}


.motivo-name {

    flex: 1;

    color: #374151;

    font-size: 14px;

}


/* ====================================================================== */
/* HORAS                                                                 */
/* ====================================================================== */

.hours-grid {

    display: grid;

    grid-template-columns:
        repeat(6, 1fr);

    gap: 12px;

    padding: 20px 24px 25px;

}


.hour-card {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 6px;

    padding: 15px 10px;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    background: #fafafa;

}


.hour-card i {

    color: #2563eb;

}


.hour-card strong {

    color: #374151;

    font-size: 14px;

}


.hour-card span {

    color: #6b7280;

    font-size: 11px;

    text-align: center;

}


/* ====================================================================== */
/* VACÍO                                                                 */
/* ====================================================================== */

.empty-message {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    min-height: 100px;

    padding: 35px 24px;

    color: #9ca3af;

    text-align: center;

    font-size: 14px;

}


/* ====================================================================== */
/* RESPONSIVE                                                             */
/* ====================================================================== */

@media (max-width: 1100px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .hours-grid {

        grid-template-columns:
            repeat(4, 1fr);

    }


    .department-detail-card {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .detail-filters {

        flex-wrap: wrap;

    }


    .search-input {

        flex-basis: 100%;

    }

}


@media (max-width: 800px) {

    .page-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }


    .two-columns {

        grid-template-columns: 1fr;

    }


    .two-columns .content-card {

        margin-bottom: 25px;

    }


    .department-row {

        grid-template-columns:
            1fr
            80px;

    }


    .department-progress {

        display: none;

    }


    .detail-filters {

        flex-direction: column;

        align-items: stretch;

    }


    .search-input {

        width: 100%;

        flex-basis: auto;

    }


    .filter-select {

        width: 100%;

    }


    .search-button {

        width: 100%;

    }


    .custom-date-filters {

        align-items: stretch;

        flex-direction: column;

    }


    .date-filter-item {

        width: 100%;

    }


    .date-input {

        width: 100%;

    }


    .period-buttons {

        flex-direction: column;

    }


    .period-button {

        width: 100%;

        justify-content: center;

    }

}


@media (max-width: 600px) {

    .departamentos-page {

        padding: 20px;

    }


    .stats-grid {

        grid-template-columns: 1fr;

    }


    .hours-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .department-detail-card {

        grid-template-columns: 1fr;

    }


    .card-header {

        align-items: flex-start;

        flex-direction: column;

        gap: 10px;

    }


    .pagination {

        gap: 10px;

    }

}

</style>
```
