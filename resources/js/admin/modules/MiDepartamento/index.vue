<template>
    <div class="departamento-page">

        <!-- ===================================================== -->
        <!-- ENCABEZADO                                            -->
        <!-- ===================================================== -->

        <MiDepartamentoHeader />


        <!-- ===================================================== -->
        <!-- PERIODO                                               -->
        <!-- ===================================================== -->

        <MiDepartamentoPeriodos
            :periodo-inicial="periodo"
            :fecha-desde-inicial="fechaDesde"
            :fecha-hasta-inicial="fechaHasta"
            @cambio-periodo="cambioPeriodo"
        />


        <!-- ===================================================== -->
        <!-- RESUMEN                                               -->
        <!-- ===================================================== -->

        <MiDepartamentoResumen
            :resumen="resumen"
        />


        <!-- ===================================================== -->
        <!-- ERROR                                                 -->
        <!-- ===================================================== -->

        <div
            v-if="error"
            class="error-message"
        >

            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ error }}
            </span>

            <button
                type="button"
                @click="actualizar"
            >
                Reintentar
            </button>

        </div>


        <!-- ===================================================== -->
        <!-- ESTADÍSTICAS                                         -->
        <!-- ===================================================== -->

        <div class="analytics-grid">

            <MiDepartamentoCategorias
                :categorias="porCategoria"
            />

            <MiDepartamentoMotivos
                :motivos="motivos"
            />

        </div>


        <!-- ===================================================== -->
        <!-- HORAS                                                 -->
        <!-- ===================================================== -->

        <MiDepartamentoHoras
            :horas="porHora"
        />


        <!-- ===================================================== -->
        <!-- LLAMADAS                                              -->
        <!-- ===================================================== -->

        <div class="calls-card">

            <div class="calls-header">

                <div>

                    <h2>
                        Llamadas del departamento
                    </h2>

                    <p>
                        Registro de llamadas recibidas por el departamento.
                    </p>

                </div>

                <button
                    type="button"
                    class="refresh-button"
                    :disabled="cargando"
                    @click="actualizar"
                >

                    <i
                        class="fas fa-sync-alt"
                        :class="{ spinning: cargando }"
                    ></i>

                    {{ cargando ? 'Actualizando...' : 'Actualizar' }}

                </button>

            </div>


            <!-- ================================================= -->
            <!-- FILTROS                                           -->
            <!-- ================================================= -->

            <MiDepartamentoFiltros
                @buscar="buscar"
            />


            <!-- ================================================= -->
            <!-- TABLA                                             -->
            <!-- ================================================= -->

            <MiDepartamentoTable
                :llamadas="llamadas"
                :cargando="cargando"
                @ver="verLlamada"
            />


            <!-- ================================================= -->
            <!-- PAGINACIÓN                                        -->
            <!-- ================================================= -->

            <div
                v-if="pagination.last_page > 1"
                class="pagination"
            >

                <button
                    type="button"
                    :disabled="
                        pagination.current_page === 1 ||
                        cargando
                    "
                    @click="
                        cambiarPagina(
                            pagination.current_page - 1
                        )
                    "
                >

                    <i class="fas fa-chevron-left"></i>

                </button>


                <span>
                    Página

                    <strong>
                        {{ pagination.current_page }}
                    </strong>

                    de

                    <strong>
                        {{ pagination.last_page }}
                    </strong>
                </span>


                <button
                    type="button"
                    :disabled="
                        pagination.current_page ===
                        pagination.last_page ||
                        cargando
                    "
                    @click="
                        cambiarPagina(
                            pagination.current_page + 1
                        )
                    "
                >

                    <i class="fas fa-chevron-right"></i>

                </button>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- DETALLE DE LLAMADA                                   -->
        <!-- ===================================================== -->

        <LlamadaDetalleModal
            :visible="mostrarDetalle"
            :llamada="llamadaSeleccionada"
            @cerrar="cerrarDetalle"
            @estado-actualizado="estadoActualizado"
        />

    </div>
</template>


<script>

import axios from 'axios';

import MiDepartamentoHeader
    from './components/MiDepartamentoHeader.vue';

import MiDepartamentoPeriodos
    from './components/MiDepartamentoPeriodos.vue';

import MiDepartamentoResumen
    from './components/MiDepartamentoResumen.vue';

import MiDepartamentoCategorias
    from './components/MiDepartamentoCategorias.vue';

import MiDepartamentoMotivos
    from './components/MiDepartamentoMotivos.vue';

import MiDepartamentoHoras
    from './components/MiDepartamentoHoras.vue';

import MiDepartamentoFiltros
    from './components/MiDepartamentoFiltros.vue';

import MiDepartamentoTable
    from './components/MiDepartamentoTable.vue';

import LlamadaDetalleModal
    from '../Llamadas/components/LlamadaDetalleModal.vue';

import './styles/mi-departamento.css';


export default {

    name: 'MiDepartamento',


    components: {

        MiDepartamentoHeader,

        MiDepartamentoPeriodos,

        MiDepartamentoResumen,

        MiDepartamentoCategorias,

        MiDepartamentoMotivos,

        MiDepartamentoHoras,

        MiDepartamentoFiltros,

        MiDepartamentoTable,

        LlamadaDetalleModal,

    },


    data() {

        return {

            // ==================================================
            // PERIODO
            // ==================================================

            periodo: 'hoy',

            fechaDesde: '',

            fechaHasta: '',


            // ==================================================
            // RESUMEN
            // ==================================================

            resumen: {

                total: 0,

                proceso: 0,

                transferidas: 0,

                finalizadas: 0,

            },


            // ==================================================
            // ESTADISTICAS
            // ==================================================

            porCategoria: [],

            motivos: [],

            porHora: [],


            // ==================================================
            // LLAMADAS
            // ==================================================

            llamadas: [],


            // ==================================================
            // ESTADO
            // ==================================================

            cargando: false,

            error: null,


            // ==================================================
            // FILTROS
            // ==================================================

            filtros: {

                busqueda: '',

                estado: '',

                categoria: '',

            },


            // ==================================================
            // PAGINACION
            // ==================================================

            pagination: {

                current_page: 1,

                last_page: 1,

                per_page: 15,

                total: 0,

            },


            // ==================================================
            // DETALLE
            // ==================================================

            mostrarDetalle: false,

            llamadaSeleccionada: null,

        };

    },


    mounted() {

        this.cargarDatos();

    },


    methods: {


        // ======================================================
        // CARGAR TODO
        // ======================================================

        async cargarDatos(page = 1) {

            this.cargando = true;

            this.error = null;

            try {

                await Promise.all([

                    this.cargarResumen(),

                    this.cargarLlamadas(page),

                ]);

            }

            catch (error) {

                console.error(
                    'Error al cargar Mi Departamento:',
                    error
                );

            }

            finally {

                this.cargando = false;

            }

        },


        // ======================================================
        // PARAMETROS DEL PERIODO
        // ======================================================

        construirParametrosPeriodo() {

            const params = {

                periodo: this.periodo,

            };


            if (
                this.periodo === 'personalizado'
            ) {

                if (this.fechaDesde) {

                    params.fecha_desde =
                        this.fechaDesde;

                }

                if (this.fechaHasta) {

                    params.fecha_hasta =
                        this.fechaHasta;

                }

            }


            return params;

        },


        // ======================================================
        // CAMBIO DE PERIODO
        // ======================================================

        cambioPeriodo(datos) {

            this.error = null;

            this.periodo =
                datos.periodo || 'hoy';

            this.fechaDesde =
                datos.fechaDesde || '';

            this.fechaHasta =
                datos.fechaHasta || '';

            this.cargarDatos(1);

        },


        // ======================================================
        // CARGAR RESUMEN + ESTADISTICAS
        // ======================================================

        async cargarResumen() {

            try {

                const params =
                    this.construirParametrosPeriodo();


                const response = await axios.get(
                    '/api/admin/llamadas/resumen',
                    {
                        params,
                    }
                );


                const data =
                    response.data.data || {};


                // ============================================
                // RESUMEN
                // ============================================

                const resumen =
                    data.resumen || {};


                this.resumen = {

                    total:
                        Number(resumen.total) || 0,

                    proceso:
                        Number(resumen.en_proceso) || 0,

                    transferidas:
                        Number(resumen.transferidas) || 0,

                    finalizadas:
                        Number(resumen.finalizadas) || 0,

                };


                // ============================================
                // CATEGORIAS
                // ============================================

                this.porCategoria =
                    Array.isArray(data.por_categoria)
                        ? data.por_categoria
                        : [];


                // ============================================
                // MOTIVOS
                // ============================================

                this.motivos =
                    Array.isArray(data.motivos)
                        ? data.motivos
                        : [];


                // ============================================
                // HORAS
                // ============================================

                this.porHora =
                    Array.isArray(data.por_hora)
                        ? data.por_hora
                        : [];

            }

            catch (error) {

                console.error(
                    'Error al cargar resumen:',
                    error
                );


                if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                }

                else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar la información de este departamento.';

                }

                else {

                    this.error =
                        'No fue posible cargar la información del departamento.';

                }


                throw error;

            }

        },


        // ======================================================
        // CARGAR LLAMADAS
        // ======================================================

        async cargarLlamadas(page = 1) {

            try {

                const params =
                    this.construirParametrosPeriodo();


                params.page = page;


                if (this.filtros.busqueda) {

                    params.search =
                        this.filtros.busqueda;

                }


                if (this.filtros.estado) {

                    params.estado =
                        this.filtros.estado;

                }


                if (this.filtros.categoria) {

                    params.categoria =
                        this.filtros.categoria;

                }


                const response = await axios.get(
                    '/api/admin/llamadas',
                    {
                        params,
                    }
                );


                const data =
                    response.data.data || {};


                this.llamadas =
                    data.data || [];


                this.pagination = {

                    current_page:
                        data.current_page || 1,

                    last_page:
                        data.last_page || 1,

                    per_page:
                        data.per_page || 15,

                    total:
                        data.total || 0,

                };

            }

            catch (error) {

                console.error(
                    'Error al cargar llamadas:',
                    error
                );


                if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                }

                else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar las llamadas.';

                }

                else {

                    this.error =
                        'No fue posible cargar las llamadas.';

                }


                throw error;

            }

        },


        // ======================================================
        // ACTUALIZAR
        // ======================================================

        actualizar() {

            this.cargarDatos(
                this.pagination.current_page
            );

        },


        // ======================================================
        // BUSCAR
        // ======================================================

        buscar(filtros) {

            this.filtros = {

                ...this.filtros,

                ...filtros,

            };


            this.cargarDatos(1);

        },


        // ======================================================
        // CAMBIAR PAGINA
        // ======================================================

        cambiarPagina(page) {

            if (
                page < 1 ||
                page > this.pagination.last_page
            ) {

                return;

            }


            this.cargarDatos(page);

        },


        // ======================================================
        // VER LLAMADA
        // ======================================================

        verLlamada(llamada) {

            this.llamadaSeleccionada =
                llamada;

            this.mostrarDetalle = true;

        },


        // ======================================================
        // CERRAR DETALLE
        // ======================================================

        cerrarDetalle() {

            this.mostrarDetalle = false;

            this.llamadaSeleccionada =
                null;

        },


        // ======================================================
        // ESTADO ACTUALIZADO
        // ======================================================

        estadoActualizado(llamadaActualizada) {

            this.llamadaSeleccionada =
                llamadaActualizada;


            this.cargarDatos(
                this.pagination.current_page
            );

        },

    },

};

</script>