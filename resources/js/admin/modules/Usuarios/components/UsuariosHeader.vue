<template>

    <div class="page-header">

        <!-- INFORMACIÓN -->

        <div>

            <h1 class="page-title">
                Usuarios
            </h1>

            <p class="page-subtitle">
                Administra los usuarios, roles y departamentos del sistema.
            </p>

        </div>


        <!-- BOTÓN NUEVO USUARIO -->

        <button
            type="button"
            class="primary-button"
            @click="nuevoUsuario"
        >

            <i class="fas fa-user-plus"></i>

            Nuevo usuario

        </button>

    </div>

</template>


<script>

export default {

    name: 'UsuariosHeader',


    methods: {

        nuevoUsuario() {

            this.$emit('nuevo-usuario');

        },

    },

};

</script>