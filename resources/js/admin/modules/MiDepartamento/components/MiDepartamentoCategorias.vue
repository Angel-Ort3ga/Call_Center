<template>
    <div class="analytics-card">

        <div class="analytics-header">
            <div>
                <h2 class="analytics-title">
                    <i class="fas fa-chart-pie"></i>
                    Llamadas por categoría
                </h2>

                <p class="analytics-subtitle">
                    Distribución de las llamadas recibidas.
                </p>
            </div>
        </div>

        <div
            v-if="!categorias.length"
            class="analytics-empty"
        >
            <i class="fas fa-chart-pie"></i>
            <span>No hay datos para este periodo.</span>
        </div>

        <div
            v-else
            class="category-list"
        >

            <div
                v-for="categoria in categorias"
                :key="categoria.categoria"
                class="category-item"
            >

                <div class="category-info">

                    <span class="category-name">
                        {{ nombreCategoria(categoria.categoria) }}
                    </span>

                    <span class="category-total">
                        {{ categoria.total }}
                    </span>

                </div>

                <div class="category-bar">

                    <div
                        class="category-bar-fill"
                        :style="{
                            width: porcentaje(categoria.total) + '%'
                        }"
                    ></div>

                </div>

                <div class="category-percent">
                    {{ porcentaje(categoria.total) }}%
                </div>

            </div>

        </div>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoCategorias',

    props: {

        categorias: {
            type: Array,
            default: () => [],
        },

    },

    computed: {

        total() {

            return this.categorias.reduce(
                (suma, categoria) =>
                    suma + Number(categoria.total || 0),
                0
            );

        },

    },

    methods: {

        porcentaje(valor) {

            if (!this.total) {
                return 0;
            }

            return Math.round(
                (Number(valor || 0) / this.total) * 100
            );

        },

        nombreCategoria(categoria) {

            const nombres = {
                informacion: 'Información',
                queja: 'Queja',
                tramite: 'Trámite',
                soporte: 'Soporte',
            };

            return nombres[categoria] || categoria;

        },

    },

};
</script>