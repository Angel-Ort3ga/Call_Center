<template>

    <div
        class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6"
    >

        <!-- ============================================================
             TÍTULO
        ============================================================= -->

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                Llamadas
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Consulta y administración de llamadas registradas
            </p>

        </div>


        <!-- ============================================================
             NUEVA LLAMADA
        ============================================================= -->

        <button
            type="button"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
            @click="$emit('nueva')"
        >

            <i class="fas fa-plus mr-2"></i>

            Nueva llamada

        </button>

    </div>

</template>


<script>

export default {

    name: 'LlamadasHeader',

};

</script>