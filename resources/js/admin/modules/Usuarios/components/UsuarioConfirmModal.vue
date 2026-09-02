<template>

    <div
        v-if="mostrar"
        class="modal-overlay"
        @click.self="cerrar"
    >

        <div class="confirm-modal">

            <!-- ===================================================== -->
            <!-- ICONO                                                 -->
            <!-- ===================================================== -->

            <div class="confirm-icon">

                <i class="fas fa-exclamation-triangle"></i>

            </div>


            <!-- ===================================================== -->
            <!-- TÍTULO                                                -->
            <!-- ===================================================== -->

            <h2>

                {{
                    accion === 'estado'
                        ? 'Cambiar estado'
                        : 'Desactivar usuario'
                }}

            </h2>


            <!-- ===================================================== -->
            <!-- MENSAJE                                               -->
            <!-- ===================================================== -->

            <p>

                {{
                    accion === 'estado'
                        ? (
                            usuario && usuario.activo
                                ? '¿Deseas desactivar a '
                                : '¿Deseas activar a '
                        )
                        : '¿Deseas desactivar a '
                }}

                <strong>

                    {{ nombreCompleto(usuario) }}

                </strong>

                ?

            </p>


            <!-- ===================================================== -->
            <!-- BOTONES                                               -->
            <!-- ===================================================== -->

            <div class="confirm-actions">

                <!-- CANCELAR -->

                <button
                    type="button"
                    class="secondary-button"
                    :disabled="procesando"
                    @click="cerrar"
                >

                    Cancelar

                </button>


                <!-- CONFIRMAR -->

                <button
                    type="button"
                    class="danger-button"
                    :disabled="procesando"
                    @click="confirmar"
                >

                    <i
                        :class="
                            procesando
                                ? 'fas fa-spinner fa-spin'
                                : 'fas fa-check'
                        "
                    ></i>

                    {{
                        procesando
                            ? 'Procesando...'
                            : 'Confirmar'
                    }}

                </button>

            </div>

        </div>

    </div>

</template>


<script>

export default {

    name: 'UsuarioConfirmModal',


    props: {

        mostrar: {

            type: Boolean,

            default: false,

        },


        usuario: {

            type: Object,

            default: null,

        },


        accion: {

            type: String,

            default: null,

        },


        procesando: {

            type: Boolean,

            default: false,

        },

    },


    methods: {

        /* ============================================================== 
         * CERRAR
         * ============================================================== */

        cerrar() {

            if (this.procesando) {

                return;

            }

            this.$emit('cerrar');

        },


        /* ============================================================== 
         * CONFIRMAR
         * ============================================================== */

        confirmar() {

            console.log(
                'BOTÓN CONFIRMAR DEL MODAL'
            );


            if (
                this.procesando ||
                !this.usuario
            ) {

                console.log(
                    'NO SE PUEDE CONFIRMAR:',
                    {
                        procesando: this.procesando,
                        usuario: this.usuario,
                    }
                );

                return;

            }


            console.log(
                'EMITIENDO EVENTO confirmar'
            );


            this.$emit('confirmar');

        },


        /* ============================================================== 
         * NOMBRE COMPLETO
         * ============================================================== */

        nombreCompleto(usuario) {

            if (!usuario) {

                return 'Sin nombre';

            }


            return (
                `${usuario.nombre || ''} ` +
                `${usuario.apellido || ''}`
            ).trim();

        },

    },

};

</script>