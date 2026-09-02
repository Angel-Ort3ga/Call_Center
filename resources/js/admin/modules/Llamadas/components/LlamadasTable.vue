<template>

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
    >

        <!-- ============================================================
             ENCABEZADO DE LA TABLA
        ============================================================= -->

        <div
            class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"
        >

            <div>

                <h2 class="font-semibold text-gray-800">
                    Registros de llamadas
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Total: {{ total }}
                </p>

            </div>


            <button
                type="button"
                class="text-sm text-blue-600 hover:text-blue-800"
                @click="$emit('actualizar')"
            >
                Actualizar
            </button>

        </div>


        <!-- ============================================================
             CARGANDO
        ============================================================= -->

        <div
            v-if="cargando"
            class="p-10 text-center text-gray-500"
        >
            Cargando llamadas...
        </div>


        <!-- ============================================================
             ERROR
        ============================================================= -->

        <div
            v-else-if="error"
            class="p-10 text-center"
        >

            <p class="text-red-600 mb-3">
                {{ error }}
            </p>

            <button
                type="button"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg"
                @click="$emit('actualizar')"
            >
                Intentar nuevamente
            </button>

        </div>


        <!-- ============================================================
             SIN RESULTADOS
        ============================================================= -->

        <div
            v-else-if="llamadas.length === 0"
            class="p-10 text-center text-gray-500"
        >
            No se encontraron llamadas.
        </div>


        <!-- ============================================================
             TABLA
        ============================================================= -->

        <div
            v-else
            class="overflow-x-auto"
        >

            <table class="w-full text-sm">

                <!-- ENCABEZADOS -->

                <thead class="bg-gray-50">

                    <tr>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Folio
                        </th>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Fecha
                        </th>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Ciudadano
                        </th>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Categoría
                        </th>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Departamento
                        </th>

                        <th
                            class="text-left px-5 py-3 font-semibold text-gray-600"
                        >
                            Estado
                        </th>

                        <th
                            class="text-right px-5 py-3 font-semibold text-gray-600"
                        >
                            Acción
                        </th>

                    </tr>

                </thead>


                <!-- CUERPO -->

                <tbody class="divide-y divide-gray-100">

                    <tr
                        v-for="llamada in llamadas"
                        :key="llamada.id"
                        class="hover:bg-gray-50"
                    >

                        <!-- FOLIO -->

                        <td class="px-5 py-4">

                            <span class="font-medium text-gray-800">
                                {{ llamada.folio }}
                            </span>

                        </td>


                        <!-- FECHA -->

                        <td class="px-5 py-4 text-gray-600">

                            {{ formatearFecha(llamada.fecha) }}

                        </td>


                        <!-- CIUDADANO -->

                        <td class="px-5 py-4">

                            <p class="font-medium text-gray-800">
                                {{ llamada.nombre }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ llamada.telefono }}
                            </p>

                        </td>


                        <!-- CATEGORÍA -->

                        <td class="px-5 py-4">

                            {{ formatearCategoria(llamada.categoria) }}

                        </td>


                        <!-- DEPARTAMENTO -->

                        <td class="px-5 py-4">

                            <span
                                v-if="llamada.departamento"
                            >
                                {{ llamada.departamento.nombre }}
                            </span>

                            <span
                                v-else
                                class="text-gray-400"
                            >
                                Sin departamento
                            </span>

                        </td>


                        <!-- ESTADO -->

                        <td class="px-5 py-4">

                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                :class="claseEstado(llamada.estado)"
                            >
                                {{ formatearEstado(llamada.estado) }}
                            </span>

                        </td>


                        <!-- ACCIÓN -->

                        <td class="px-5 py-4 text-right">

                            <button
                                type="button"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                                @click="$emit('ver', llamada)"
                            >
                                Ver
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- ============================================================
             PAGINACIÓN
        ============================================================= -->

        <div
            v-if="pagination.last_page > 1"
            class="px-5 py-4 border-t border-gray-200 flex items-center justify-between"
        >

            <span class="text-sm text-gray-500">

                Página {{ pagination.current_page }}
                de {{ pagination.last_page }}

            </span>


            <div class="flex gap-2">

                <button
                    type="button"
                    class="px-3 py-2 text-sm border rounded-lg disabled:opacity-40"
                    :disabled="pagination.current_page === 1"
                    @click="$emit(
                        'pagina',
                        pagination.current_page - 1
                    )"
                >
                    Anterior
                </button>


                <button
                    type="button"
                    class="px-3 py-2 text-sm border rounded-lg disabled:opacity-40"
                    :disabled="
                        pagination.current_page ===
                        pagination.last_page
                    "
                    @click="$emit(
                        'pagina',
                        pagination.current_page + 1
                    )"
                >
                    Siguiente
                </button>

            </div>

        </div>

    </div>

</template>


<script>

export default {

    name: 'LlamadasTable',


    props: {

        llamadas: {
            type: Array,
            default: () => [],
        },

        pagination: {
            type: Object,
            required: true,
        },

        total: {
            type: Number,
            default: 0,
        },

        cargando: {
            type: Boolean,
            default: false,
        },

        error: {
            type: String,
            default: null,
        },

    },


    methods: {

        /* ============================================================
           FORMATEAR FECHA
        ============================================================= */

        formatearFecha(fecha) {

    if (!fecha) {
        return '-';
    }

    const valor = String(fecha).substring(0, 10);

    const partes = valor.split('-');

    if (partes.length === 3) {

        return `${partes[2]}/${partes[1]}/${partes[0]}`;

    }

    return valor;

},


        /* ============================================================
           FORMATEAR CATEGORÍA
        ============================================================= */

        formatearCategoria(categoria) {

            const categorias = {

                informacion: 'Información',

                queja: 'Queja',

                tramite: 'Trámite',

                soporte: 'Soporte',

            };

            return categorias[categoria] || categoria;

        },


        /* ============================================================
           FORMATEAR ESTADO
        ============================================================= */

        formatearEstado(estado) {

            const estados = {

                en_proceso: 'En proceso',

                transferida: 'Transferida',

                finalizada: 'Finalizada',

            };

            return estados[estado] || estado;

        },


        /* ============================================================
           CLASE DEL ESTADO
        ============================================================= */

        claseEstado(estado) {

            const clases = {

                en_proceso:
                    'bg-yellow-100 text-yellow-800',

                transferida:
                    'bg-blue-100 text-blue-800',

                finalizada:
                    'bg-green-100 text-green-800',

            };

            return clases[estado]
                || 'bg-gray-100 text-gray-800';

        },

    },

};

</script>