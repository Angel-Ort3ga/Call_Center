<template>
    <div
        v-if="mostrar"
        class="modal-overlay"
        @click.self="cerrar"
    >
        <div
            class="confirm-modal"
            :class="{
                'status-deactivate': usuario && usuario.activo,
                'status-activate': usuario && !usuario.activo
            }"
        >

            <!-- ICONO -->
            <div class="confirm-icon">
                <span class="confirm-symbol">
                    !
                </span>
            </div>

            <!-- TÍTULO -->
            <h2>
                {{
                    usuario && usuario.activo
                        ? 'Desactivar usuario'
                        : 'Activar usuario'
                }}
            </h2>

            <!-- MENSAJE -->
            <p>
                {{
                    usuario && usuario.activo
                        ? '¿Seguro que deseas desactivar al usuario '
                        : '¿Deseas activar al usuario '
                }}

                <strong>
                    {{ nombreCompleto(usuario) }}
                </strong>

                ?
            </p>

            <!-- ADVERTENCIA -->
            <div class="confirm-warning">
                <span>
                    {{
                        usuario && usuario.activo
                            ? 'El estado de la cuenta cambiará a Inactivo.'
                            : 'El estado de la cuenta cambiará a Activo.'
                    }}
                </span>
            </div>

            <!-- BOTONES -->
            <div class="confirm-actions">

                <button
                    type="button"
                    class="secondary-button"
                    :disabled="procesando"
                    @click="cerrar"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    :class="
                        usuario && usuario.activo
                            ? 'danger-button'
                            : 'success-button'
                    "
                    :disabled="procesando"
                    @click="confirmar"
                >

                    <span v-if="procesando">
                        Procesando...
                    </span>

                    <span v-else>
                        {{
                            usuario && usuario.activo
                                ? 'Desactivar usuario'
                                : 'Activar usuario'
                        }}
                    </span>

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
            default: 'estado',
        },

        procesando: {
            type: Boolean,
            default: false,
        },
    },

    methods: {

        cerrar() {
            if (this.procesando) {
                return;
            }

            this.$emit('cerrar');
        },

        confirmar() {
            if (this.procesando || !this.usuario) {
                return;
            }

            this.$emit('confirmar');
        },

        nombreCompleto(usuario) {
            if (!usuario) {
                return 'Sin nombre';
            }

            return `${usuario.nombre || ''} ${usuario.apellido || ''}`.trim();
        },
    },
};
</script>