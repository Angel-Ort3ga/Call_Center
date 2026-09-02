```vue
<template>

    <div>

        <!-- ========================================================= -->
        <!-- TABLA                                                     -->
        <!-- ========================================================= -->

        <div
            v-if="usuarios.length"
            class="table-container"
        >

            <table class="users-table">

                <thead>

                    <tr>

                        <th>
                            Usuario
                        </th>

                        <th>
                            Correo
                        </th>

                        <th>
                            Rol
                        </th>

                        <th>
                            Departamento
                        </th>

                        <th>
                            Teléfono
                        </th>

                        <th>
                            Estado
                        </th>

                        <th class="actions-column">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="usuario in usuarios"
                        :key="usuario.id"
                    >

                        <!-- ================================================= -->
                        <!-- USUARIO                                             -->
                        <!-- ================================================= -->

                        <td>

                            <div class="user-info">

                                <div class="user-avatar">

                                    <img
                                        v-if="usuario.foto"
                                        :src="usuario.foto"
                                        alt="Foto"
                                    >

                                    <span v-else>
                                        {{ iniciales(usuario) }}
                                    </span>

                                </div>


                                <div>

                                    <strong>
                                        {{ nombreCompleto(usuario) }}
                                    </strong>

                                    <span>
                                        @{{ usuario.username }}
                                    </span>

                                </div>

                            </div>

                        </td>


                        <!-- ================================================= -->
                        <!-- CORREO                                              -->
                        <!-- ================================================= -->

                        <td>

                            <span class="email-text">
                                {{ usuario.email || '—' }}
                            </span>

                        </td>


                        <!-- ================================================= -->
                        <!-- ROL                                                  -->
                        <!-- ================================================= -->

                        <td>

                            <span class="role-badge">
                                {{ nombreRol(usuario.role) }}
                            </span>

                        </td>


                        <!-- ================================================= -->
                        <!-- DEPARTAMENTO                                         -->
                        <!-- ================================================= -->

                        <td>

                            {{
                                usuario.departamento
                                    ? usuario.departamento.nombre
                                    : 'Sin departamento'
                            }}

                        </td>


                        <!-- ================================================= -->
                        <!-- TELÉFONO                                             -->
                        <!-- ================================================= -->

                        <td>

                            {{ usuario.telefono || '—' }}

                        </td>


                        <!-- ================================================= -->
                        <!-- ESTADO                                               -->
                        <!-- ================================================= -->

                        <td>

                            <span
                                class="status-badge"
                                :class="
                                    usuario.activo
                                        ? 'status-active'
                                        : 'status-inactive'
                                "
                            >

                                <span class="status-dot"></span>

                                {{
                                    usuario.activo
                                        ? 'Activo'
                                        : 'Inactivo'
                                }}

                            </span>

                        </td>


                        <!-- ================================================= -->
                        <!-- ACCIONES                                              -->
                        <!-- ================================================= -->

                        <td>

                            <div class="actions">

                                <!-- ================================================= -->
                                <!-- EDITAR                                            -->
                                <!-- ================================================= -->

                                <button
                                    type="button"
                                    class="action-button action-edit"
                                    title="Editar usuario"
                                    @click="editarUsuario(usuario)"
                                >

                                    <span class="action-icon">
                                        ✏
                                    </span>

                                </button>


                                <!-- ================================================= -->
                                <!-- ACTIVAR / DESACTIVAR                              -->
                                <!-- ================================================= -->

                                <button
                                    type="button"
                                    class="action-button"
                                    :class="
                                        usuario.activo
                                            ? 'action-warning'
                                            : 'action-success'
                                    "
                                    :title="
                                        usuario.activo
                                            ? 'Desactivar usuario'
                                            : 'Activar usuario'
                                    "
                                    @click="cambiarEstado(usuario)"
                                >

                                    <span class="action-icon">
                                        {{
                                            usuario.activo
                                                ? '⏸'
                                                : '✓'
                                        }}
                                    </span>

                                </button>


                                <!-- ================================================= -->
                                <!-- DESACTIVAR CUENTA                                 -->
                                <!-- ================================================= -->

                                <button
                                    type="button"
                                    class="action-button action-danger"
                                    title="Desactivar cuenta"
                                    @click="desactivarUsuario(usuario)"
                                >

                                    <span class="action-icon">
                                        🗑
                                    </span>

                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- ========================================================= -->
        <!-- SIN RESULTADOS                                             -->
        <!-- ========================================================= -->

        <div
            v-else
            class="empty-message"
        >

            <i class="fas fa-users"></i>

            <div>

                <strong>
                    No se encontraron usuarios
                </strong>

                <span>
                    Intenta cambiar los filtros de búsqueda.
                </span>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PAGINACIÓN                                                 -->
        <!-- ========================================================= -->

        <div
            v-if="totalPaginas > 1"
            class="pagination"
        >

            <button
                type="button"
                class="pagination-button"
                :disabled="paginaActual <= 1"
                @click="cambiarPagina(paginaActual - 1)"
            >

                <i class="fas fa-chevron-left"></i>

            </button>


            <button
                v-for="(pagina, index) in paginasVisibles"
                :key="index"
                type="button"
                class="pagination-number"
                :class="{
                    active: pagina === paginaActual
                }"
                :disabled="pagina === '...'"
                @click="cambiarPagina(pagina)"
            >

                {{ pagina }}

            </button>


            <button
                type="button"
                class="pagination-button"
                :disabled="paginaActual >= totalPaginas"
                @click="cambiarPagina(paginaActual + 1)"
            >

                <i class="fas fa-chevron-right"></i>

            </button>

        </div>

    </div>

</template>


<script>

export default {

    name: 'UsuariosTable',


    props: {

        usuarios: {
            type: Array,
            default: () => [],
        },

        cargando: {
            type: Boolean,
            default: false,
        },

        totalUsuarios: {
            type: Number,
            default: 0,
        },

        paginaActual: {
            type: Number,
            default: 1,
        },

        totalPaginas: {
            type: Number,
            default: 1,
        },

    },


    computed: {

        paginasVisibles() {

            const paginas = [];

            const total = this.totalPaginas;

            const actual = this.paginaActual;


            if (total <= 7) {

                for (
                    let i = 1;
                    i <= total;
                    i++
                ) {

                    paginas.push(i);

                }

                return paginas;

            }


            paginas.push(1);


            const inicio =
                Math.max(
                    2,
                    actual - 2
                );


            const fin =
                Math.min(
                    total - 1,
                    actual + 2
                );


            if (inicio > 2) {

                paginas.push('...');

            }


            for (
                let i = inicio;
                i <= fin;
                i++
            ) {

                paginas.push(i);

            }


            if (fin < total - 1) {

                paginas.push('...');

            }


            paginas.push(total);


            return paginas;

        },

    },


    methods: {

        /* ========================================================== */
        /* EDITAR                                                     */
        /* ========================================================== */

        editarUsuario(usuario) {

            this.$emit(
                'editar',
                usuario
            );

        },


        /* ========================================================== */
        /* CAMBIAR ESTADO                                              */
        /* ========================================================== */

        cambiarEstado(usuario) {

            this.$emit(
                'cambiar-estado',
                usuario
            );

        },


        /* ========================================================== */
        /* DESACTIVAR CUENTA                                           */
        /* ========================================================== */

        desactivarUsuario(usuario) {

            this.$emit(
                'desactivar',
                usuario
            );

        },


        /* ========================================================== */
        /* PAGINACIÓN                                                  */
        /* ========================================================== */

        cambiarPagina(pagina) {

            if (
                pagina === '...'
            ) {

                return;

            }


            if (
                pagina < 1 ||
                pagina > this.totalPaginas
            ) {

                return;

            }


            this.$emit(
                'cambiar-pagina',
                pagina
            );

        },


        /* ========================================================== */
        /* NOMBRE COMPLETO                                             */
        /* ========================================================== */

        nombreCompleto(usuario) {

            if (!usuario) {

                return 'Sin nombre';

            }


            return (
                `${usuario.nombre || ''} ` +
                `${usuario.apellido || ''}`
            ).trim();

        },


        /* ========================================================== */
        /* INICIALES                                                   */
        /* ========================================================== */

        iniciales(usuario) {

            if (!usuario) {

                return 'U';

            }


            const primera =
                (usuario.nombre || '').charAt(0);


            const segunda =
                (usuario.apellido || '').charAt(0);


            return (
                primera +
                segunda
            ).toUpperCase() || 'U';

        },


        /* ========================================================== */
        /* NOMBRE DEL ROL                                              */
        /* ========================================================== */

        nombreRol(rol) {

            if (!rol) {

                return 'Sin rol';

            }


            const nombre =
                rol.nombre ||
                rol.name ||
                rol.slug ||
                '';


            const nombres = {

                super_admin:
                    'Super Administrador',

                supervisor:
                    'Supervisor',

                jefe_departamento:
                    'Jefe de departamento',

                recepcionista:
                    'Recepcionista',

            };


            return (
                nombres[rol.slug] ||
                nombres[nombre] ||
                nombre
            );

        },

    },

};

</script>
```
