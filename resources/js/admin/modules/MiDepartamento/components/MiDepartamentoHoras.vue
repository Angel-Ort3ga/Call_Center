<template>
    <div class="analytics-card">

        <div class="analytics-header">
            <div>
                <h2 class="analytics-title">
                    <i class="fas fa-clock"></i>
                    Horario de llamadas
                </h2>

                <p class="analytics-subtitle">
                    Distribución de llamadas por hora.
                </p>
            </div>
        </div>

        <div
            v-if="!horas.length"
            class="analytics-empty"
        >
            <i class="fas fa-clock"></i>
            <span>No hay datos para este periodo.</span>
        </div>

        <div
            v-else
            class="hours-chart"
        >

            <div
                v-for="hora in horas"
                :key="hora.hora"
                class="hour-item"
            >

                <div class="hour-value">
                    {{ hora.total }}
                </div>

                <div class="hour-bar-container">

                    <div
                        class="hour-bar"
                        :style="{
                            height: porcentaje(hora.total) + '%'
                        }"
                    ></div>

                </div>

                <div class="hour-label">
                    {{ formatearHora(hora.hora) }}
                </div>

            </div>

        </div>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoHoras',

    props: {

        horas: {
            type: Array,
            default: () => [],
        },

    },

    computed: {

        maximo() {

            if (!this.horas.length) {
                return 0;
            }

            return Math.max(
                ...this.horas.map(
                    hora => Number(hora.total || 0)
                )
            );

        },

    },

    methods: {

        porcentaje(valor) {

            if (!this.maximo) {
                return 0;
            }

            return Math.max(
                8,
                Math.round(
                    (Number(valor || 0) / this.maximo) * 100
                )
            );

        },

        formatearHora(hora) {

            const numero = Number(hora);

            if (Number.isNaN(numero)) {
                return hora;
            }

            return String(numero).padStart(2, '0') + ':00';

        },

    },

};
</script>