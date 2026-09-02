```vue
<template>

    <div class="departamento-detalle">

        <!-- ============================================================ -->
        <!-- INFORMACIÓN DEL DEPARTAMENTO -->
        <!-- ============================================================ -->

        <div class="department-detail-card">

            <div class="detail-item">

                <span>
                    Código
                </span>

                <strong>
                    {{ departamento.codigo || '—' }}
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Extensión
                </span>

                <strong>
                    {{ departamento.extension || '—' }}
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Teléfono
                </span>

                <strong>
                    {{ departamento.telefono || '—' }}
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Correo
                </span>

                <strong>
                    {{ departamento.correo || '—' }}
                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Responsable
                </span>

                <strong>

                    {{
                        departamento.responsable
                            ? departamento.responsable.name
                            : 'Sin responsable'
                    }}

                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Horario
                </span>

                <strong>

                    {{
                        formatearHorario(
                            departamento.horario_inicio,
                            departamento.horario_fin
                        )
                    }}

                </strong>

            </div>


            <div class="detail-item">

                <span>
                    Estado
                </span>

                <strong>

                    <span
                        class="status-badge"
                        :class="
                            departamento.activo
                                ? 'status-active'
                                : 'status-inactive'
                        "
                    >

                        {{
                            departamento.activo
                                ? 'Activo'
                                : 'Inactivo'
                        }}

                    </span>

                </strong>

            </div>

        </div>


        <!-- ============================================================ -->
        <!-- FILTROS -->
        <!-- ============================================================ -->

        <div class="content-card">

            <div class="card-header">

                <div>

                    <h2>
                        Buscar llamadas
                    </h2>

                    <p>
                        Filtra las llamadas registradas en este departamento.
                    </p>

                </div>

            </div>


            <div class="detail-filters">

                <input
                    v-model="localBusqueda"
                    type="text"
                    class="search-input"
                    placeholder="Buscar por folio, nombre, teléfono, categoría..."
                    @keyup.enter="buscar"
                >


                <select
                    v-model="localEstado"
                    class="filter-select"
                    @change="buscar"
                >

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="en_proceso">
                        En proceso
                    </option>

                    <option value="transferida">
                        Transferida
                    </option>

                    <option value="finalizada">
                        Finalizada
                    </option>

                </select>


                <select
                    v-model="localPeriodo"
                    class="filter-select"
                    @change="cambioPeriodo"
                >

                    <option value="hoy">
                        Hoy
                    </option>

                    <option value="semana">
                        Esta semana
                    </option>

                    <option value="mes">
                        Este mes
                    </option>

                    <option value="todo">
                        Todo
                    </option>

                    <option value="personalizado">
                        Personalizado
                    </option>

                </select>


                <button
                    type="button"
                    class="search-button"
                    :disabled="cargando"
                    @click="buscar"
                >

                    <i class="fas fa-search"></i>

                    Buscar

                </button>

            </div>


            <!-- FECHAS PERSONALIZADAS -->

            <div
                v-if="localPeriodo === 'personalizado'"
                class="custom-date-filters"
            >

                <div class="date-filter-item">

                    <label>
                        Desde
                    </label>

                    <input
                        v-model="localFechaDesde"
                        type="date"
                        class="date-input"
                    >

                </div>


                <div class="date-filter-item">

                    <label>
                        Hasta
                    </label>

                    <input
                        v-model="localFechaHasta"
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

                    Aplicar periodo

                </button>

            </div>

        </div>

    </div>

</template>


<script>

export default {

    name: 'DepartamentoModal',


    props: {

        departamento: {

            type: Object,

            required: true,

        },


        busqueda: {

            type: String,

            default: '',

        },


        estado: {

            type: String,

            default: '',

        },


        periodo: {

            type: String,

            default: 'hoy',

        },


        fechaDesde: {

            type: String,

            default: '',

        },


        fechaHasta: {

            type: String,

            default: '',

        },


        cargando: {

            type: Boolean,

            default: false,

        },

    },


    data() {

        return {

            localBusqueda:
                this.busqueda,

            localEstado:
                this.estado,

            localPeriodo:
                this.periodo,

            localFechaDesde:
                this.fechaDesde,

            localFechaHasta:
                this.fechaHasta,

        };

    },


    watch: {

        busqueda(valor) {

            this.localBusqueda =
                valor;

        },


        estado(valor) {

            this.localEstado =
                valor;

        },


        periodo(valor) {

            this.localPeriodo =
                valor;

        },


        fechaDesde(valor) {

            this.localFechaDesde =
                valor;

        },


        fechaHasta(valor) {

            this.localFechaHasta =
                valor;

        },

    },


    methods: {

        buscar() {

            this.$emit(

                'buscar',

                {

                    busqueda:
                        this.localBusqueda,

                    estado:
                        this.localEstado,

                    periodo:
                        this.localPeriodo,

                    fechaDesde:
                        this.localFechaDesde,

                    fechaHasta:
                        this.localFechaHasta,

                }

            );

        },


        cambioPeriodo() {

            if (
                this.localPeriodo ===
                'personalizado'
            ) {

                this.$emit(

                    'cambio-periodo',

                    this.localPeriodo

                );

                return;

            }


            this.buscar();

        },


        aplicarPeriodo() {

            if (
                !this.localFechaDesde ||
                !this.localFechaHasta
            ) {

                return;

            }


            if (
                this.localFechaDesde >
                this.localFechaHasta
            ) {

                return;

            }


            this.buscar();

        },


        formatearHorario(inicio, fin) {

            if (
                !inicio ||
                !fin
            ) {

                return '—';

            }


            return (

                String(inicio)
                    .substring(0, 5)

                + ' - '

                + String(fin)
                    .substring(0, 5)

            );

        },

    },

};

</script>
```
