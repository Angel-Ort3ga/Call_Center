<template>
    <div class="table-container">

        <table>

            <thead>

                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th></th>
                </tr>

            </thead>

            <tbody>

                <!-- CARGANDO -->
                <tr v-if="cargando">

                    <td
                        colspan="8"
                        class="table-message"
                    >
                        <i class="fas fa-spinner fa-spin"></i>

                        Cargando llamadas...
                    </td>

                </tr>

                <!-- SIN LLAMADAS -->
                <tr
                    v-else-if="!llamadas.length"
                >

                    <td
                        colspan="8"
                        class="table-message"
                    >
                        <i class="fas fa-phone-slash"></i>

                        No hay llamadas para mostrar.
                    </td>

                </tr>

                <!-- LLAMADAS -->
                <tr
                    v-for="llamada in llamadas"
                    v-else
                    :key="llamada.id"
                >

                    <td>
                        <span class="folio">
                            {{ llamada.folio }}
                        </span>
                    </td>

                    <td>
                        {{ formatearFecha(llamada.fecha) }}
                    </td>

                    <td>
                        {{ formatearHora(llamada.hora) }}
                    </td>

                    <td>
                        {{ llamada.nombre || '—' }}
                    </td>

                    <td>
                        {{ llamada.telefono || '—' }}
                    </td>

                    <td>
                        {{ formatearCategoria(llamada.categoria) }}
                    </td>

                    <td>

                        <span
                            class="status"
                            :class="llamada.estado"
                        >
                            {{ nombreEstado(llamada.estado) }}
                        </span>

                    </td>

                    <td>

                        <button
                            type="button"
                            class="view-button"
                            @click="$emit('ver', llamada)"
                        >
                            <i class="fas fa-eye"></i>

                            Ver
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoTable',

    props: {

        llamadas: {
            type: Array,
            required: true,
        },

        cargando: {
            type: Boolean,
            default: false,
        },
    },

    methods: {

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

        formatearHora(hora) {

            if (!hora) {
                return '—';
            }

            return String(hora).substring(0, 5);
        },

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
    },
};
</script>