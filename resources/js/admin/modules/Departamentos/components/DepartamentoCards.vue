```vue
<template>

    <div>

        <!-- ============================================================ -->
        <!-- TARJETAS -->
        <!-- ============================================================ -->

        <div class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon total">
                    <i class="fas fa-phone"></i>
                </div>

                <div>
                    <span class="stat-label">
                        Total de llamadas
                    </span>

                    <strong class="stat-value">
                        {{ resumen.total }}
                    </strong>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon process">
                    <i class="fas fa-clock"></i>
                </div>

                <div>
                    <span class="stat-label">
                        En proceso
                    </span>

                    <strong class="stat-value">
                        {{ resumen.en_proceso }}
                    </strong>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon transferred">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <div>
                    <span class="stat-label">
                        Transferidas
                    </span>

                    <strong class="stat-value">
                        {{ resumen.transferidas }}
                    </strong>
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon finished">
                    <i class="fas fa-check"></i>
                </div>

                <div>
                    <span class="stat-label">
                        Finalizadas
                    </span>

                    <strong class="stat-value">
                        {{ resumen.finalizadas }}
                    </strong>
                </div>

            </div>

        </div>


        <!-- ============================================================ -->
        <!-- CATEGORÍAS + MOTIVOS -->
        <!-- ============================================================ -->

        <div class="two-columns">

            <!-- CATEGORÍAS -->

            <div class="content-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Categorías
                        </h2>

                        <p>
                            Motivos clasificados por categoría.
                        </p>

                    </div>

                </div>


                <div
                    v-if="porCategoria.length"
                    class="category-list"
                >

                    <div
                        v-for="categoria in porCategoria"
                        :key="categoria.categoria"
                        class="category-row"
                    >

                        <div class="category-name">

                            {{
                                formatearCategoria(
                                    categoria.categoria
                                )
                            }}

                        </div>


                        <div class="category-bar-container">

                            <div class="category-bar-background">

                                <div
                                    class="category-bar"
                                    :style="{
                                        width:
                                            porcentaje(
                                                categoria.total
                                            ) + '%'
                                    }"
                                ></div>

                            </div>

                        </div>


                        <strong>
                            {{ categoria.total }}
                        </strong>

                    </div>

                </div>


                <div
                    v-else
                    class="empty-message"
                >
                    No hay categorías registradas.
                </div>

            </div>


            <!-- MOTIVOS -->

            <div class="content-card">

                <div class="card-header">

                    <div>

                        <h2>
                            Motivos frecuentes
                        </h2>

                        <p>
                            Principales motivos de las llamadas.
                        </p>

                    </div>

                </div>


                <div
                    v-if="motivos.length"
                    class="motivos-list"
                >

                    <div
                        v-for="(motivo, index) in motivos"
                        :key="index"
                        class="motivo-row"
                    >

                        <span class="motivo-position">
                            {{ index + 1 }}
                        </span>

                        <span class="motivo-name">
                            {{ motivo.motivo }}
                        </span>

                        <strong>
                            {{ motivo.total }}
                        </strong>

                    </div>

                </div>


                <div
                    v-else
                    class="empty-message"
                >
                    No hay motivos registrados.
                </div>

            </div>

        </div>


        <!-- ============================================================ -->
        <!-- HORAS PICO -->
        <!-- ============================================================ -->

        <div class="content-card">

            <div class="card-header">

                <div>

                    <h2>
                        Horas de mayor actividad
                    </h2>

                    <p>
                        Cantidad de llamadas recibidas por hora.
                    </p>

                </div>

            </div>


            <div
                v-if="porHora.length"
                class="hours-grid"
            >

                <div
                    v-for="hora in porHora"
                    :key="hora.hora"
                    class="hour-card"
                >

                    <i class="fas fa-clock"></i>

                    <strong>
                        {{ formatearHora(hora.hora) }}
                    </strong>

                    <span>

                        {{ hora.total }}

                        {{
                            hora.total === 1
                                ? 'llamada'
                                : 'llamadas'
                        }}

                    </span>

                </div>

            </div>


            <div
                v-else
                class="empty-message"
            >
                No hay llamadas registradas por hora.
            </div>

        </div>

    </div>

</template>


<script>

export default {

    name: 'DepartamentoStatsCards',


    props: {

        resumen: {

            type: Object,

            default: () => ({

                total: 0,

                en_proceso: 0,

                transferidas: 0,

                finalizadas: 0,

            }),

        },


        porCategoria: {

            type: Array,

            default: () => [],

        },


        motivos: {

            type: Array,

            default: () => [],

        },


        porHora: {

            type: Array,

            default: () => [],

        },

    },


    methods: {

        porcentaje(total) {

            if (!this.resumen.total) {

                return 0;

            }


            return Math.min(

                100,

                Math.round(

                    (
                        Number(total) /
                        Number(this.resumen.total)
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


        formatearHora(hora) {

            if (
                hora === null ||
                hora === undefined ||
                hora === ''
            ) {

                return '—';

            }


            const numero =
                Number(hora);


            if (
                Number.isNaN(numero)
            ) {

                return String(hora);

            }


            return (
                String(numero)
                    .padStart(2, '0')
                + ':00'
            );

        },

    },

};

</script>
```
