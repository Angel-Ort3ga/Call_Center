<template>
    <div class="filters">

        <!-- Busqueda -->

        <div class="search-box">
            <i class="fas fa-search"></i>

            <input
                v-model="busqueda"
                type="text"
                placeholder="Buscar llamada..."
                @keyup.enter="buscar"
            >
        </div>

        <!-- Estado -->

        <select
            v-model="estado"
            class="filter-select"
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

        <!-- Categoria -->

        <select
            v-model="categoria"
            class="filter-select"
        >
            <option value="">
                Todas las categorías
            </option>

            <option value="queja">
                Queja
            </option>

            <option value="informacion">
                Información
            </option>

            <option value="tramite">
                Trámite
            </option>

            <option value="soporte">
                Soporte
            </option>
        </select>

        <!-- Buscar -->

        <button
            type="button"
            class="search-button"
            @click="buscar"
        >
            <i class="fas fa-search"></i>
            Buscar
        </button>

    </div>
</template>

<script>
export default {
    name: 'MiDepartamentoFiltros',

    data() {
        return {
            busqueda: '',
            estado: '',
            categoria: '',
        };
    },

    methods: {
        buscar() {
            this.$emit('buscar', {
                busqueda: this.busqueda.trim(),
                estado: this.estado,
                categoria: this.categoria,
            });
        },

        limpiar() {
            this.busqueda = '';
            this.estado = '';
            this.categoria = '';

            this.$emit('buscar', {
                busqueda: '',
                estado: '',
                categoria: '',
            });
        },
    },
};
</script>