```vue
<template>

    <div class="page-header">

        <!-- ============================================================ -->
        <!-- HEADER GENERAL -->
        <!-- ============================================================ -->

        <template v-if="!departamento">

            <div>

                <h1 class="page-title">
                    Departamentos
                </h1>

                <p class="page-subtitle">
                    Resumen general de llamadas y actividad por departamento.
                </p>

            </div>

        </template>


        <!-- ============================================================ -->
        <!-- HEADER DETALLE -->
        <!-- ============================================================ -->

        <template v-else>

            <div>

                <button
                    type="button"
                    class="back-button"
                    @click="$emit('volver')"
                >

                    <i class="fas fa-arrow-left"></i>

                    Volver a departamentos

                </button>


                <h1 class="page-title">
                    {{ departamento.nombre }}
                </h1>


                <p class="page-subtitle">
                    Información y llamadas registradas en este departamento.
                </p>

            </div>

        </template>


        <!-- ============================================================ -->
        <!-- ACTUALIZAR -->
        <!-- ============================================================ -->

        <div class="header-actions">

            <button
                type="button"
                class="refresh-button"
                :disabled="cargando"
                @click="$emit('actualizar')"
            >

                <i class="fas fa-sync-alt"></i>

                {{
                    cargando
                        ? 'Actualizando...'
                        : 'Actualizar'
                }}

            </button>

        </div>

    </div>

</template>


<script>

export default {

    name: 'DepartamentoHeader',


    props: {

        departamento: {

            type: Object,

            default: null,

        },


        cargando: {

            type: Boolean,

            default: false,

        },

    },

};

</script>
```
