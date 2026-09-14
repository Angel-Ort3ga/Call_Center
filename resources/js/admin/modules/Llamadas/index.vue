<template>

    <div class="p-6">

        <!-- ============================================================
             ENCABEZADO
        ============================================================= -->

        <LlamadasHeader
            @nueva="nuevaLlamada"
        />


        <!-- ============================================================
             FORMULARIO
        ============================================================= -->

        <div
            v-if="mostrarFormulario"
            class="mb-6"
        >

            <LlamadaFormModal
                @cancelar="cerrarFormulario"
                @guardado="llamadaGuardada"
            />

        </div>


        <!-- ============================================================
             FILTROS
        ============================================================= -->

        <LlamadasFiltros
            :filtros="filtros"
            @actualizar="actualizarFiltro"
            @aplicar="aplicarFiltros"
            @limpiar="limpiarFiltros"
        />


        <!-- ============================================================
             TABLA
        ============================================================= -->

        <LlamadasTable
            :llamadas="llamadas"
            :pagination="pagination"
            :total="totalLlamadas"
            :cargando="cargando"
            :error="error"
            @actualizar="cargarLlamadas"
            @pagina="cambiarPagina"
            @ver="verLlamada"
        />


        <!-- ============================================================
             DETALLE
        ============================================================= -->

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


import LlamadasHeader
    from './components/LlamadasHeader.vue';

import LlamadasFiltros
    from './components/LlamadasFiltros.vue';

import LlamadasTable
    from './components/LlamadasTable.vue';

import LlamadaFormModal
    from './components/LlamadaFormModal.vue';

import LlamadaDetalleModal
    from './components/LlamadaDetalleModal.vue';


//import './styles/Llamadas.css';


export default {

    name: 'Llamadas',


    components: {

        LlamadasHeader,

        LlamadasFiltros,

        LlamadasTable,

        LlamadaFormModal,

        LlamadaDetalleModal,

    },


    data() {

        return {

            /* ========================================================
               FORMULARIO
            ======================================================== */

            mostrarFormulario: false,


            /* ========================================================
               DETALLE
            ======================================================== */

            mostrarDetalle: false,

            llamadaSeleccionada: null,


            /* ========================================================
               LLAMADAS
            ======================================================== */

            llamadas: [],


            /* ========================================================
               ESTADO
            ======================================================== */

            cargando: false,

            error: null,


            /* ========================================================
               FILTROS
            ======================================================== */

            filtros: {

                busqueda: '',

                estado: '',

                categoria: '',

            },


            /* ========================================================
               PAGINACIÓN
            ======================================================== */

            pagination: {

                current_page: 1,

                last_page: 1,

                per_page: 15,

                total: 0,

            },

        };

    },


    computed: {

        totalLlamadas() {

            return this.pagination.total;

        },

    },


    mounted() {

        this.cargarLlamadas();

    },


    methods: {

        /* ============================================================
           CARGAR LLAMADAS
        ============================================================= */

        async cargarLlamadas(page = 1) {

            this.cargando = true;

            this.error = null;


            try {

                const params = {

                    page,

                };


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


                const response =
                    await axios.get(
                        '/api/admin/llamadas',
                        {
                            params,
                        }
                    );


                const data =
                    response.data.data;


                this.llamadas =
                    data.data || [];


                this.pagination = {

                    current_page:
                        data.current_page,

                    last_page:
                        data.last_page,

                    per_page:
                        data.per_page,

                    total:
                        data.total,

                };

            } catch (error) {

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

                } else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar las llamadas.';

                } else {

                    this.error =
                        'No fue posible cargar las llamadas.';

                }

            } finally {

                this.cargando = false;

            }

        },


        /* ============================================================
           FILTROS
        ============================================================= */

        actualizarFiltro({ campo, valor }) {

            this.filtros = {

                ...this.filtros,

                [campo]: valor,

            };

        },


        aplicarFiltros() {

            this.cargarLlamadas(1);

        },


        limpiarFiltros() {

            this.filtros = {

                busqueda: '',

                estado: '',

                categoria: '',

            };


            this.cargarLlamadas(1);

        },


        /* ============================================================
           PAGINACIÓN
        ============================================================= */

        cambiarPagina(page) {

            if (
                page < 1 ||
                page > this.pagination.last_page
            ) {

                return;

            }


            this.cargarLlamadas(page);

        },


        /* ============================================================
           NUEVA LLAMADA
        ============================================================= */

        nuevaLlamada() {

            this.mostrarFormulario = true;


            window.scrollTo({

                top: 0,

                behavior: 'smooth',

            });

        },


        cerrarFormulario() {

            this.mostrarFormulario = false;

        },


        llamadaGuardada() {

            this.mostrarFormulario = false;


            this.cargarLlamadas(1);

        },


        /* ============================================================
           DETALLE
        ============================================================= */

        verLlamada(llamada) {

            this.llamadaSeleccionada =
                llamada;


            this.mostrarDetalle = true;

        },


        cerrarDetalle() {

            this.mostrarDetalle = false;

            this.llamadaSeleccionada = null;

        },

        estadoActualizado(llamadaActualizada) {

    this.llamadaSeleccionada = llamadaActualizada;

    this.cargarLlamadas(
        this.pagination.current_page
    );

},

    },

};

</script>