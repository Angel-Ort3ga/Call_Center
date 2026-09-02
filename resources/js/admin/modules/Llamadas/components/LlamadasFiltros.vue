<template>

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6"
    >

        <!-- ============================================================
             FILTROS
        ============================================================= -->

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- BUSCAR -->

            <div class="md:col-span-2">

                <label
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Buscar
                </label>

                <div class="relative">

                    <input
                        :value="filtros.busqueda"
                        type="text"
                        placeholder="Folio, nombre o teléfono..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        @input="
                            actualizar(
                                'busqueda',
                                $event.target.value
                            )
                        "
                        @keyup.enter="$emit('aplicar')"
                    >

                    <span
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"
                    >
                        <i class="fas fa-search"></i>
                    </span>

                </div>

            </div>


            <!-- ESTADO -->

            <div>

                <label
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Estado
                </label>

                <select
                    :value="filtros.estado"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    @change="
                        actualizar(
                            'estado',
                            $event.target.value
                        )
                    "
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

            </div>


            <!-- CATEGORÍA -->

            <div>

                <label
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Categoría
                </label>

                <select
                    :value="filtros.categoria"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    @change="
                        actualizar(
                            'categoria',
                            $event.target.value
                        )
                    "
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

            </div>

        </div>


        <!-- ============================================================
             BOTONES
        ============================================================= -->

        <div class="flex justify-end gap-3 mt-4">

            <button
                type="button"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                :disabled="cargando"
                @click="$emit('limpiar')"
            >

                <i class="fas fa-eraser mr-2"></i>

                Limpiar

            </button>


            <button
                type="button"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition disabled:opacity-50"
                :disabled="cargando"
                @click="$emit('aplicar')"
            >

                <i
                    :class="
                        cargando
                            ? 'fas fa-spinner fa-spin mr-2'
                            : 'fas fa-search mr-2'
                    "
                ></i>

                {{ cargando ? 'Buscando...' : 'Buscar' }}

            </button>

        </div>

    </div>

</template>


<script>

export default {

    name: 'LlamadasFiltros',


    props: {

        filtros: {
            type: Object,
            required: true,
        },

        cargando: {
            type: Boolean,
            default: false,
        },

    },


    methods: {

        actualizar(campo, valor) {

            this.$emit(
                'actualizar',
                {
                    campo,
                    valor,
                }
            );

        },

    },

};

</script>