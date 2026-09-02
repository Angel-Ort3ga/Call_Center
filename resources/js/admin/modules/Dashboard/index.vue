<template>
    <div>
        <!-- Encabezado -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard
            </h1>

            <p class="mt-1 text-gray-500">
                Resumen de llamadas del Call Center
            </p>
        </div>

        <!-- Selector de fecha -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <label
                        for="fecha"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Fecha
                    </label>

                    <input
                        id="fecha"
                        v-model="fecha"
                        type="date"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="cargarDashboard"
                    >
                </div>

                <div
                    v-if="cargando"
                    class="text-sm text-gray-500"
                >
                    Cargando...
                </div>
            </div>
        </div>

        <!-- Error -->
        <div
            v-if="error"
            class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6"
        >
            {{ error }}
        </div>

        <!-- Tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

            <!-- Total -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">
                    Llamadas de hoy
                </p>

                <p class="mt-2 text-3xl font-bold text-gray-800">
                    {{ llamadas.total }}
                </p>
            </div>

            <!-- En proceso -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">
                    En proceso
                </p>

                <p class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ llamadas.en_proceso }}
                </p>
            </div>

            <!-- Transferidas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">
                    Transferidas
                </p>

                <p class="mt-2 text-3xl font-bold text-blue-600">
                    {{ llamadas.transferidas }}
                </p>
            </div>

            <!-- Finalizadas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <p class="text-sm font-medium text-gray-500">
                    Finalizadas
                </p>

                <p class="mt-2 text-3xl font-bold text-green-600">
                    {{ llamadas.finalizadas }}
                </p>
            </div>
        </div>

        <!-- Información -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">

            <!-- Por departamento -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Llamadas por departamento
                </h2>

                <div
                    v-if="estadisticas.por_departamento.length === 0"
                    class="text-sm text-gray-500"
                >
                    No hay llamadas registradas para esta fecha.
                </div>

                <div
                    v-for="item in estadisticas.por_departamento"
                    :key="item.departamento_id"
                    class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
                >
                    <span class="text-sm text-gray-700">
                        {{ item.departamento || 'Sin departamento' }}
                    </span>

                    <span class="font-semibold text-gray-800">
                        {{ item.total }}
                    </span>
                </div>
            </div>

            <!-- Por categoría -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">

                <h2 class="text-lg font-semibold text-gray-800 mb-4">
                    Llamadas por categoría
                </h2>

                <div
                    v-if="estadisticas.por_categoria.length === 0"
                    class="text-sm text-gray-500"
                >
                    No hay llamadas registradas para esta fecha.
                </div>

                <div
                    v-for="item in estadisticas.por_categoria"
                    :key="item.categoria"
                    class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
                >
                    <span class="text-sm text-gray-700">
                        {{ item.categoria }}
                    </span>

                    <span class="font-semibold text-gray-800">
                        {{ item.total }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Por hora -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mt-6">

            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Llamadas por hora
            </h2>

            <div
                v-if="estadisticas.por_hora.length === 0"
                class="text-sm text-gray-500"
            >
                No hay llamadas registradas para esta fecha.
            </div>

            <div
                v-for="item in estadisticas.por_hora"
                :key="item.hora"
                class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
            >
                <span class="text-sm text-gray-700">
                    {{ item.hora }}
                </span>

                <span class="font-semibold text-gray-800">
                    {{ item.total }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Dashboard',

    data() {
        return {
            fecha: this.obtenerFechaActual(),

            cargando: false,

            error: null,

            llamadas: {
                total: 0,
                en_proceso: 0,
                transferidas: 0,
                finalizadas: 0,
            },

            estadisticas: {
                por_departamento: [],
                por_categoria: [],
                por_hora: [],
            },
        };
    },

    mounted() {
        this.cargarDashboard();
    },

    methods: {
        obtenerFechaActual() {
            const fecha = new Date();

            const year = fecha.getFullYear();

            const month = String(
                fecha.getMonth() + 1
            ).padStart(2, '0');

            const day = String(
                fecha.getDate()
            ).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        async cargarDashboard() {
            this.cargando = true;

            this.error = null;

            try {
                const response = await axios.get(
                    '/api/admin/dashboard',
                    {
                        params: {
                            fecha: this.fecha,
                        },
                    }
                );

                const data = response.data.data;

                this.llamadas = {
                    total: data.llamadas.total,
                    en_proceso: data.llamadas.en_proceso,
                    transferidas: data.llamadas.transferidas,
                    finalizadas: data.llamadas.finalizadas,
                };

                this.estadisticas = {
                    por_departamento:
                        data.estadisticas.por_departamento || [],

                    por_categoria:
                        data.estadisticas.por_categoria || [],

                    por_hora:
                        data.estadisticas.por_hora || [],
                };

            } catch (error) {

                console.error(
                    'Error al cargar el dashboard:',
                    error
                );

                this.error =
                    'No fue posible cargar las estadísticas del dashboard.';
            } finally {
                this.cargando = false;
            }
        },
    },
};
</script>