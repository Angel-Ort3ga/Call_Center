```vue
<template>

    <div>

        <!-- ============================================================ -->
        <!-- LISTA DE DEPARTAMENTOS -->
        <!-- ============================================================ -->

        <div
            v-if="!departamentoSeleccionado"
            class="content-card"
        >

            <div class="card-header">

                <div>

                    <h2>
                        Llamadas por departamento
                    </h2>

                    <p>
                        Distribución de llamadas durante el periodo seleccionado.
                    </p>

                </div>

                <span class="card-hint">
                    Haz clic en un departamento para ver sus llamadas
                </span>

            </div>


            <div
                v-if="departamentos.length"
                class="departments-list"
            >

                <div
                    v-for="departamento in departamentos"
                    :key="departamento.id"
                    class="department-row"
                    @click="$emit('seleccionar', departamento)"
                >

                    <div class="department-info">

                        <div class="department-icon">

                            <i class="fas fa-building"></i>

                        </div>

                        <div>

                            <strong>
                                {{ departamento.nombre }}
                            </strong>

                            <span>

                                {{ departamento.total }}

                                {{
                                    departamento.total === 1
                                        ? 'llamada'
                                        : 'llamadas'
                                }}

                            </span>

                        </div>

                    </div>


                    <div class="department-progress">

                        <div class="progress-background">

                            <div
                                class="progress-bar"
                                :style="{
                                    width:
                                        porcentaje(
                                            departamento.total
                                        ) + '%'
                                }"
                            ></div>

                        </div>

                    </div>


                    <div class="department-action">

                        <strong>
                            {{ departamento.total }}
                        </strong>

                        <i class="fas fa-chevron-right"></i>

                    </div>

                </div>

            </div>


            <div
                v-else
                class="empty-message"
            >

                <i class="fas fa-building"></i>

                No hay llamadas registradas para el periodo seleccionado.

            </div>

        </div>


        <!-- ============================================================ -->
        <!-- TABLA DE LLAMADAS -->
        <!-- ============================================================ -->

        <div
            v-if="departamentoSeleccionado"
            class="content-card"
        >

            <div class="card-header">

                <div>

                    <h2>
                        Llamadas
                    </h2>

                    <p>
                        Registro de llamadas de
                        {{ departamentoSeleccionado.nombre }}.
                    </p>

                </div>


                <div class="calls-count">

                    {{ total }}

                    {{
                        total === 1
                            ? 'llamada'
                            : 'llamadas'
                    }}

                </div>

            </div>


            <!-- CARGANDO -->

            <div
                v-if="cargando"
                class="empty-message"
            >

                <i class="fas fa-spinner fa-spin"></i>

                Cargando llamadas...

            </div>


            <!-- TABLA -->

            <div
                v-else-if="llamadas.length"
                class="table-container"
            >

                <table class="calls-table">

                    <thead>

                        <tr>

                            <th>
                                Folio
                            </th>

                            <th>
                                Fecha
                            </th>

                            <th>
                                Hora
                            </th>

                            <th>
                                Nombre
                            </th>

                            <th>
                                Teléfono
                            </th>

                            <th>
                                Categoría
                            </th>

                            <th>
                                Estado
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="llamada in llamadas"
                            :key="llamada.id"
                        >

                            <td>

                                <strong>
                                    {{ llamada.folio }}
                                </strong>

                            </td>

                            <td>
                                {{ formatearFecha(llamada.fecha) }}
                            </td>

                            <td>
                                {{ formatearHora(llamada.hora) }}
                            </td>

                            <td>
                                {{ llamada.nombre || '—' }}
                            </td>

                            <td>
                                {{ llamada.telefono || '—' }}
                            </td>

                            <td>
                                {{ formatearCategoria(llamada.categoria) }}
                            </td>

                            <td>

                                <span
                                    class="status-badge"
                                    :class="estadoClase(llamada.estado)"
                                >

                                    {{ formatearEstado(llamada.estado) }}

                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>


                <!-- PAGINACIÓN -->

                <div
                    v-if="totalPaginas > 1"
                    class="pagination"
                >

                    <button
                        type="button"
                        class="pagination-button"
                        :disabled="
                            pagina <= 1 ||
                            cargando
                        "
                        @click="$emit('pagina', pagina - 1)"
                    >

                        <i class="fas fa-chevron-left"></i>

                    </button>


                    <span class="pagination-info">

                        Página

                        <strong>
                            {{ pagina }}
                        </strong>

                        de

                        <strong>
                            {{ totalPaginas }}
                        </strong>

                    </span>


                    <button
                        type="button"
                        class="pagination-button"
                        :disabled="
                            pagina >= totalPaginas ||
                            cargando
                        "
                        @click="$emit('pagina', pagina + 1)"
                    >

                        <i class="fas fa-chevron-right"></i>

                    </button>

                </div>

            </div>


            <!-- SIN RESULTADOS -->

            <div
                v-else
                class="empty-message"
            >

                <i class="fas fa-phone-slash"></i>

                No hay llamadas que coincidan con los filtros.

            </div>

        </div>

    </div>

</template>


<script>

export default {

    name: 'DepartamentoTable',


    props: {

        departamentos: {

            type: Array,

            default: () => [],

        },


        resumenTotal: {

            type: Number,

            default: 0,

        },


        departamentoSeleccionado: {

            type: Object,

            default: null,

        },


        llamadas: {

            type: Array,

            default: () => [],

        },


        cargando: {

            type: Boolean,

            default: false,

        },


        total: {

            type: Number,

            default: 0,

        },


        pagina: {

            type: Number,

            default: 1,

        },


        totalPaginas: {

            type: Number,

            default: 1,

        },

    },


    methods: {

        porcentaje(total) {

            if (!this.resumenTotal) {

                return 0;

            }


            return Math.min(

                100,

                Math.round(

                    (
                        Number(total) /
                        Number(this.resumenTotal)
                    ) * 100

                )

            );

        },


        formatearCategoria(categoria) {

            if (!categoria) {

                return 'Sin categoría';

            }


            return String(categoria)

                .replace(
                    /_/g,
                    ' '
                )

                .replace(
                    /\b\w/g,
                    letra =>
                        letra.toUpperCase()
                );

        },


        formatearEstado(estado) {

            const estados = {

                en_proceso:
                    'En proceso',

                transferida:
                    'Transferida',

                finalizada:
                    'Finalizada',

            };


            return (

                estados[estado] ||

                estado ||

                'Sin estado'

            );

        },


        estadoClase(estado) {

            return {

                'status-process':
                    estado === 'en_proceso',

                'status-transferred':
                    estado === 'transferida',

                'status-finished':
                    estado === 'finalizada',

            };

        },


        formatearHora(hora) {

            if (!hora) {

                return '—';

            }


            return String(hora)
                .substring(0, 5);

        },


        formatearFecha(fecha) {

            if (!fecha) {

                return '—';

            }


            const partes =
                String(fecha)
                    .substring(0, 10)
                    .split('-');


            if (
                partes.length !== 3
            ) {

                return fecha;

            }


            return (

                partes[2]
                + '/'
                + partes[1]
                + '/'
                + partes[0]

            );

        },

    },

};

</script>
```
