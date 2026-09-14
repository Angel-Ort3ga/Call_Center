<template>
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Mi Departamento
            </h1>

            <p class="page-subtitle">
                Consulta y seguimiento de las llamadas recibidas.
            </p>
        </div>

        <div class="department-name">
            <i class="fas fa-building"></i>

            <span>
                {{ nombreDepartamento }}
            </span>
        </div>

    </div>
</template>

<script>
import { getUser } from '../../../../auth/auth';

export default {

    name: 'MiDepartamentoHeader',

    data() {
        return {
            usuario: null,
        };
    },

    computed: {

        nombreDepartamento() {

            if (!this.usuario) {
                return 'Mi departamento';
            }

            return this.usuario.departamento
                || 'Sin departamento asignado';
        },
    },

    mounted() {

        this.usuario = getUser();
    },
};
</script>