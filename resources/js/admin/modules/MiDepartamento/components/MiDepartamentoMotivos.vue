<template>
    <div class="analytics-card">

        <div class="analytics-header">
            <div>
                <h2 class="analytics-title">
                    <i class="fas fa-list-alt"></i>
                    Principales motivos
                </h2>

                <p class="analytics-subtitle">
                    Motivos más frecuentes de las llamadas.
                </p>
            </div>
        </div>

        <div
            v-if="!motivosOrdenados.length"
            class="analytics-empty"
        >
            <i class="fas fa-list-alt"></i>
            <span>No hay datos para este periodo.</span>
        </div>

        <div
            v-else
            class="motives-list"
        >

            <div
                v-for="(motivo, index) in motivosOrdenados"
                :key="index"
                class="motive-item"
            >

                <div class="motive-number">
                    {{ index + 1 }}
                </div>

                <div class="motive-content">

                    <div class="motive-text">
                        {{ motivo.motivo }}
                    </div>

                    <div class="motive-bar">

                        <div
                            class="motive-bar-fill"
                            :style="{
                                width: porcentaje(motivo.total) + '%'
                            }"
                        ></div>

                    </div>

                </div>

                <div class="motive-total">
                    {{ motivo.total }}
                </div>

            </div>

        </div>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoMotivos',

    props: {

        motivos: {
            type: Array,
            default: () => [],
        },

    },

    computed: {

        motivosOrdenados() {

            return [...this.motivos]
                .sort(
                    (a, b) =>
                        Number(b.total || 0) -
                        Number(a.total || 0)
                )
                .slice(0, 8);

        },

        maximo() {

            if (!this.motivosOrdenados.length) {
                return 0;
            }

            return Math.max(
                ...this.motivosOrdenados.map(
                    motivo => Number(motivo.total || 0)
                )
            );

        },

    },

    methods: {

        porcentaje(valor) {

            if (!this.maximo) {
                return 0;
            }

            return Math.round(
                (Number(valor || 0) / this.maximo) * 100
            );

        },

    },

};
</script>