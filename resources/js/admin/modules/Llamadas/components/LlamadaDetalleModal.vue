```vue
<template>

    <div
        v-if="visible && llamada"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
        @click.self="$emit('cerrar')"
    >

        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col"
        >

            <!-- ========================================================
                 HEADER
            ========================================================= -->

            <div
                class="px-6 py-5 border-b border-gray-200 flex items-center justify-between"
            >

                <div>

                    <h2 class="text-xl font-bold text-gray-800">
                        Detalle de llamada
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Información completa de la llamada.
                    </p>

                </div>

                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
                    @click="$emit('cerrar')"
                >
                    <i class="fas fa-times"></i>
                </button>

            </div>


            <!-- ========================================================
                 CONTENIDO
            ========================================================= -->

            <div class="p-6 overflow-y-auto">

                <!-- FOLIO -->

                <div
                    class="bg-gray-50 border border-gray-200 rounded-lg px-5 py-4 mb-6 flex items-center justify-between"
                >

                    <span class="text-sm font-medium text-gray-500">
                        Folio
                    </span>

                    <strong class="text-lg font-semibold text-gray-800">
                        {{ llamada.folio || '—' }}
                    </strong>

                </div>


                <!-- ====================================================
                     INFORMACIÓN
                ===================================================== -->

                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-5"
                >

                    <!-- FECHA -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Fecha
                        </span>

                        <strong class="text-sm text-gray-800">
                            {{ formatearFecha(llamada.fecha) }}
                        </strong>

                    </div>


                    <!-- HORA -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Hora
                        </span>

                        <strong class="text-sm text-gray-800">
                            {{ formatearHora(llamada.hora) }}
                        </strong>

                    </div>


                    <!-- NOMBRE -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Nombre
                        </span>

                        <strong class="text-sm text-gray-800">
                            {{ llamada.nombre || '—' }}
                        </strong>

                    </div>


                    <!-- TELÉFONO -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Teléfono
                        </span>

                        <strong class="text-sm text-gray-800">
                            {{ llamada.telefono || '—' }}
                        </strong>

                    </div>


                    <!-- CATEGORÍA -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Categoría
                        </span>

                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                            :class="claseCategoria(llamada.categoria)"
                        >
                            {{ formatearCategoria(llamada.categoria) }}
                        </span>

                    </div>


                    <!-- DEPARTAMENTO -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Departamento
                        </span>

                        <strong class="text-sm text-gray-800">

                            {{
                                llamada.departamento
                                    ? llamada.departamento.nombre
                                    : 'Sin departamento'
                            }}

                        </strong>

                    </div>


                    <!-- ESTADO -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Estado
                        </span>

                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                            :class="claseEstado(llamada.estado)"
                        >
                            {{ nombreEstado(llamada.estado) }}
                        </span>

                    </div>


                    <!-- REGISTRADA POR -->

                    <div>

                        <span class="block text-xs font-medium text-gray-500 mb-1">
                            Registrada por
                        </span>

                        <strong class="text-sm text-gray-800">

                            {{
                                llamada.usuario
                                    ? nombreUsuario(llamada.usuario)
                                    : '—'
                            }}

                        </strong>

                    </div>

                </div>


                <!-- ====================================================
                     MOTIVO
                ===================================================== -->

                <div class="mt-7">

                    <span
                        class="block text-sm font-semibold text-gray-800 mb-2"
                    >
                        Motivo de la llamada
                    </span>

                    <div
                        class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line"
                    >
                        {{ llamada.motivo || 'Sin información.' }}
                    </div>

                </div>


                <!-- ====================================================
                     OBSERVACIONES
                ===================================================== -->

                <div class="mt-5">

                    <span
                        class="block text-sm font-semibold text-gray-800 mb-2"
                    >
                        Observaciones
                    </span>

                    <div
                        class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line"
                    >
                        {{ llamada.observaciones || 'Sin observaciones.' }}
                    </div>

                </div>


                <!-- ====================================================
                     ACCIONES DE ESTADO
                ===================================================== -->

                <div
                    v-if="puedeCambiarEstado"
                    class="mt-7 pt-6 border-t border-gray-200"
                >

                    <div class="mb-4">

                        <h3 class="text-sm font-semibold text-gray-800">
                            Acción de la llamada
                        </h3>

                        <p class="text-xs text-gray-500 mt-1">
                            Cambia el estado de acuerdo con tu función.
                        </p>

                    </div>


                    <!-- RECEPCIONISTA -->

                    <button
                        v-if="puedeTransferir"
                        type="button"
                        class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="actualizandoEstado"
                        @click="cambiarEstado('transferida')"
                    >

                        <span v-if="actualizandoEstado">
                            Procesando...
                        </span>

                        <span v-else>
                            <i class="fas fa-share mr-2"></i>
                            Transferir llamada
                        </span>

                    </button>


                    <!-- JEFE DE DEPARTAMENTO -->

                    <button
                        v-if="puedeFinalizar"
                        type="button"
                        class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="actualizandoEstado"
                        @click="cambiarEstado('finalizada')"
                    >

                        <span v-if="actualizandoEstado">
                            Procesando...
                        </span>

                        <span v-else>
                            <i class="fas fa-check mr-2"></i>
                            Finalizar llamada
                        </span>

                    </button>


                    <!-- SUPERVISOR / SUPER ADMIN -->

                    <div
                        v-if="esAdministrador"
                        class="grid grid-cols-1 sm:grid-cols-3 gap-3"
                    >

                        <button
                            type="button"
                            class="px-4 py-3 bg-yellow-500 text-white rounded-lg font-medium hover:bg-yellow-600 transition disabled:opacity-50"
                            :disabled="actualizandoEstado || llamada.estado === 'en_proceso'"
                            @click="cambiarEstado('en_proceso')"
                        >
                            En proceso
                        </button>

                        <button
                            type="button"
                            class="px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50"
                            :disabled="actualizandoEstado || llamada.estado === 'transferida'"
                            @click="cambiarEstado('transferida')"
                        >
                            Transferida
                        </button>

                        <button
                            type="button"
                            class="px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50"
                            :disabled="actualizandoEstado || llamada.estado === 'finalizada'"
                            @click="cambiarEstado('finalizada')"
                        >
                            Finalizada
                        </button>

                    </div>


                    <!-- ERROR -->

                    <div
                        v-if="errorEstado"
                        class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"
                    >
                        {{ errorEstado }}
                    </div>

                </div>

            </div>


            <!-- ========================================================
                 FOOTER
            ========================================================= -->

            <div
                class="px-6 py-4 border-t border-gray-200 flex justify-end"
            >

                <button
                    type="button"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                    @click="$emit('cerrar')"
                >
                    Cerrar
                </button>

            </div>

        </div>

    </div>

</template>


<script>

import axios from 'axios';
import { getUser } from '../../../../auth/auth';

export default {

    name: 'LlamadaDetalleModal',


    props: {

        visible: {
            type: Boolean,
            default: false,
        },

        llamada: {
            type: Object,
            default: null,
        },

    },


    data() {

        return {

            usuario: null,

            actualizandoEstado: false,

            errorEstado: null,

        };

    },


    computed: {

        /* ============================================================
           ROL ACTUAL
        ============================================================= */

        rolUsuario() {

            if (
                !this.usuario ||
                !this.usuario.role
            ) {
                return null;
            }

            return this.usuario.role.slug;

        },


        /* ============================================================
           RECEPCIONISTA
        ============================================================= */

        puedeTransferir() {

            return (
                this.rolUsuario === 'recepcionista' &&
                this.llamada &&
                this.llamada.estado === 'en_proceso'
            );

        },


        /* ============================================================
           JEFE DE DEPARTAMENTO
        ============================================================= */

        puedeFinalizar() {

            return (
                this.rolUsuario === 'jefe_departamento' &&
                this.llamada &&
                this.llamada.estado === 'transferida'
            );

        },


        /* ============================================================
           SUPERVISOR / SUPER ADMIN
        ============================================================= */

        esAdministrador() {

            return (
                this.rolUsuario === 'super_admin' ||
                this.rolUsuario === 'supervisor'
            );

        },


        /* ============================================================
           ¿MOSTRAR ACCIONES?
        ============================================================= */

        puedeCambiarEstado() {

            return (
                this.puedeTransferir ||
                this.puedeFinalizar ||
                this.esAdministrador
            );

        },

    },


    watch: {

        visible(valor) {

            if (valor) {
                this.cargarUsuario();
            }

        },

    },


    methods: {

        /* ============================================================
           USUARIO AUTENTICADO
        ============================================================= */

        cargarUsuario() {

            this.usuario = getUser();
console.log('USUARIO AUTENTICADO:', this.usuario);
    console.log('ROL:', this.usuario?.role);
    console.log('SLUG:', this.usuario?.role?.slug);
        },


        /* ============================================================
           CAMBIAR ESTADO
        ============================================================= */

        async cambiarEstado(nuevoEstado) {

            if (
                !this.llamada ||
                this.actualizandoEstado
            ) {
                return;
            }

            const mensajes = {

                transferida:
                    '¿Deseas transferir esta llamada al departamento?',

                finalizada:
                    '¿Deseas marcar esta llamada como finalizada?',

                en_proceso:
                    '¿Deseas regresar esta llamada a estado en proceso?',

            };

            const confirmar = window.confirm(
                mensajes[nuevoEstado]
                    || '¿Deseas cambiar el estado de la llamada?'
            );

            if (!confirmar) {
                return;
            }

            this.actualizandoEstado = true;
            this.errorEstado = null;

            try {

                const response = await axios.patch(
                    `/api/admin/llamadas/${this.llamada.id}/estado`,
                    {
                        estado: nuevoEstado,
                    }
                );

                const llamadaActualizada =
                    response.data.data;

                this.$emit(
                    'estado-actualizado',
                    llamadaActualizada
                );

            } catch (error) {

                console.error(
                    'Error al cambiar estado:',
                    error
                );

                if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.errorEstado =
                        error.response.data.message
                        || 'No tienes permisos para realizar esta acción.';

                } else if (
                    error.response &&
                    error.response.status === 422
                ) {

                    this.errorEstado =
                        'Los datos enviados no son válidos.';

                } else {

                    this.errorEstado =
                        'No fue posible actualizar el estado de la llamada.';

                }

            } finally {

                this.actualizandoEstado = false;

            }

        },


        /* ============================================================
           FECHA
        ============================================================= */

        formatearFecha(fecha) {

            if (!fecha) {
                return '—';
            }

            const valor =
                String(fecha).substring(0, 10);

            const partes =
                valor.split('-');

            if (partes.length === 3) {

                return `${partes[2]}/${partes[1]}/${partes[0]}`;

            }

            return valor;

        },


        /* ============================================================
           HORA
        ============================================================= */

        formatearHora(hora) {

            if (!hora) {
                return '—';
            }

            return String(hora).substring(0, 5);

        },


        /* ============================================================
           CATEGORÍA
        ============================================================= */

        formatearCategoria(categoria) {

            const categorias = {

                queja: 'Queja',

                informacion: 'Información',

                tramite: 'Trámite',

                soporte: 'Soporte',

            };

            return categorias[categoria]
                || categoria
                || '—';

        },


        claseCategoria(categoria) {

            const clases = {

                queja:
                    'bg-red-100 text-red-700',

                informacion:
                    'bg-blue-100 text-blue-700',

                tramite:
                    'bg-purple-100 text-purple-700',

                soporte:
                    'bg-green-100 text-green-700',

            };

            return clases[categoria]
                || 'bg-gray-100 text-gray-700';

        },


        /* ============================================================
           ESTADO
        ============================================================= */

        nombreEstado(estado) {

            const estados = {

                en_proceso: 'En proceso',

                transferida: 'Transferida',

                finalizada: 'Finalizada',

            };

            return estados[estado]
                || estado
                || 'Sin estado';

        },


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


        /* ============================================================
           USUARIO
        ============================================================= */

        nombreUsuario(usuario) {

            if (!usuario) {
                return '—';
            }

            const nombre =
                usuario.nombre || '';

            const apellido =
                usuario.apellido || '';

            return (
                `${nombre} ${apellido}`.trim()
                || usuario.username
                || '—'
            );

        },

    },

};

</script>
```
