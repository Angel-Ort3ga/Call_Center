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
