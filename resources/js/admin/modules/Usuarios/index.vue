```vue
<template>

    <div class="usuarios-page">

        <!-- ========================================================= -->
        <!-- ENCABEZADO                                                -->
        <!-- ========================================================= -->

        <UsuariosHeader
            @nuevo-usuario="abrirFormulario"
        />


        <!-- ========================================================= -->
        <!-- MENSAJE DE ERROR                                          -->
        <!-- ========================================================= -->

        <div
            v-if="error"
            class="alert alert-error"
        >

            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ error }}
            </span>

            <button
                type="button"
                @click="error = null"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>


        <!-- ========================================================= -->
        <!-- MENSAJE DE ÉXITO                                          -->
        <!-- ========================================================= -->

        <div
            v-if="mensaje"
            class="alert alert-success"
        >

            <i class="fas fa-check-circle"></i>

            <span>
                {{ mensaje }}
            </span>

            <button
                type="button"
                @click="mensaje = null"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>


        <!-- ========================================================= -->
        <!-- FILTROS                                                   -->
        <!-- ========================================================= -->

        <div class="content-card">

            <div class="card-header">

                <div>

                    <h2>
                        Buscar usuarios
                    </h2>

                    <p>
                        Utiliza los filtros para encontrar un usuario.
                    </p>

                </div>

            </div>


            <div class="filters">

                <!-- BUSCAR -->

                <div class="filter-search">

                    <i class="fas fa-search"></i>

                    <input
                        v-model="filtros.search"
                        type="text"
                        placeholder="Nombre, usuario o correo..."
                        @keyup.enter="buscar"
                    >

                </div>


                <!-- ROL -->

                <select
                    v-model="filtros.role_id"
                    class="filter-select"
                    @change="buscar"
                >

                    <option value="">
                        Todos los roles
                    </option>

                    <option
                        v-for="rol in roles"
                        :key="rol.id"
                        :value="rol.id"
                    >
                        {{ nombreRol(rol) }}
                    </option>

                </select>


                <!-- DEPARTAMENTO -->

                <select
                    v-model="filtros.departamento_id"
                    class="filter-select"
                    @change="buscar"
                >

                    <option value="">
                        Todos los departamentos
                    </option>

                    <option
                        v-for="departamento in departamentos"
                        :key="departamento.id"
                        :value="departamento.id"
                    >
                        {{ departamento.nombre }}
                    </option>

                </select>


                <!-- ESTADO -->

                <select
                    v-model="filtros.activo"
                    class="filter-select"
                    @change="buscar"
                >

                    <option value="">
                        Todos los estados
                    </option>

                    <option value="1">
                        Activos
                    </option>

                    <option value="0">
                        Inactivos
                    </option>

                </select>


                <!-- BUSCAR -->

                <button
                    type="button"
                    class="search-button"
                    :disabled="cargando"
                    @click="buscar"
                >

                    <i
                        :class="
                            cargando
                                ? 'fas fa-spinner fa-spin'
                                : 'fas fa-search'
                        "
                    ></i>

                    Buscar

                </button>


                <!-- LIMPIAR -->

                <button
                    type="button"
                    class="clear-button"
                    :disabled="cargando"
                    @click="limpiarFiltros"
                >

                    <i class="fas fa-eraser"></i>

                    Limpiar

                </button>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- TABLA                                                     -->
        <!-- ========================================================= -->

        <div class="content-card">

            <div class="card-header">

                <div>

                    <h2>
                        Usuarios registrados
                    </h2>

                    <p>
                        Lista de usuarios del sistema.
                    </p>

                </div>


                <div class="total-badge">

                    {{ totalUsuarios }}

                    {{
                        totalUsuarios === 1
                            ? 'usuario'
                            : 'usuarios'
                    }}

                </div>

            </div>


            <!-- CARGANDO -->

            <div
                v-if="cargando"
                class="loading-container"
            >

                <i class="fas fa-spinner fa-spin"></i>

                <span>
                    Cargando usuarios...
                </span>

            </div>


            <!-- TABLA -->

            <UsuariosTable
                v-else
                :usuarios="usuarios"
                :cargando="cargando"
                :total-usuarios="totalUsuarios"
                :total-paginas="totalPaginas"
                :pagina-actual="paginaActual"
                @editar="editarUsuario"
                @cambiar-estado="cambiarEstado"
                @cambiar-pagina="cambiarPagina"
            />

        </div>


        <!-- ========================================================= -->
        <!-- MODAL CREAR / EDITAR                                      -->
        <!-- ========================================================= -->

        <UsuarioFormModal
            v-if="mostrarFormulario"
            :mostrar="mostrarFormulario"
            :form="form"
            :roles="roles"
            :departamentos="departamentos"
            :errores="errores"
            :guardando="guardando"
            :modo-edicion="modoEdicion"
            @guardar="guardarUsuario"
            @cerrar="cerrarFormulario"
        />


        <!-- ========================================================= -->
        <!-- MODAL CONFIRMACIÓN                                         -->
        <!-- ========================================================= -->

        <UsuarioConfirmModal
            v-if="usuarioConfirmacion"
            :usuario="usuarioConfirmacion"
            :accion="accionConfirmacion"
            :procesando="procesandoAccion"
            :nombre-completo="nombreCompleto"
            @confirmar="confirmarAccion"
            @cerrar="cerrarConfirmacion"
        />

    </div>

</template>


<script>

import axios from 'axios';

import UsuariosHeader
    from './components/UsuariosHeader.vue';

import UsuariosTable
    from './components/UsuariosTable.vue';

import UsuarioFormModal
    from './components/UsuarioFormModal.vue';

import UsuarioConfirmModal
    from './components/UsuarioConfirmModal.vue';


export default {

    name: 'UsuariosIndex',


    components: {

        UsuariosHeader,

        UsuariosTable,

        UsuarioFormModal,

        UsuarioConfirmModal,

    },


    data() {

        return {

            usuarios: [],

            roles: [],

            departamentos: [],


            filtros: {

                search: '',

                role_id: '',

                departamento_id: '',

                activo: '',

                per_page: 10,

            },


            paginaActual: 1,

            totalPaginas: 1,

            totalUsuarios: 0,


            cargando: false,

            guardando: false,

            procesandoAccion: false,

            error: null,

            mensaje: null,


            mostrarFormulario: false,

            modoEdicion: false,

            usuarioEditando: null,


            form: {

                id: null,

                nombre: '',

                apellido: '',

                username: '',

                email: '',

                telefono: '',

                extension: '',

                password: '',

                role_id: '',

                departamento_id: '',

                activo: true,

            },


            errores: {},


            usuarioConfirmacion: null,

            accionConfirmacion: null,

        };

    },


    mounted() {

        this.cargarDatosIniciales();

    },


    methods: {


        /* =========================================================
           DATOS INICIALES
        ========================================================= */

        async cargarDatosIniciales() {

            await Promise.all([

                this.cargarUsuarios(),

                this.cargarRoles(),

                this.cargarDepartamentos(),

            ]);

        },


        /* =========================================================
           USUARIOS
        ========================================================= */

        async cargarUsuarios() {

            this.cargando = true;

            this.error = null;


            try {

                const response =
                    await axios.get(
                        '/api/admin/usuarios',
                        {
                            params: {
                                ...this.filtros,
                                page: this.paginaActual,
                            },
                        }
                    );


                const data =
                    response.data.data ||
                    response.data;


                /*
                 * Laravel paginator:
                 *
                 * response.data.data
                 *     -> objeto paginator
                 *
                 * paginator.data
                 *     -> usuarios
                 */

                this.usuarios =
                    data.data || [];


                this.totalUsuarios =
                    data.total || 0;


                this.paginaActual =
                    data.current_page || 1;


                this.totalPaginas =
                    data.last_page || 1;


            } catch (error) {

                console.error(
                    'Error al cargar usuarios:',
                    error
                );


                this.usuarios = [];

                this.totalUsuarios = 0;

                this.totalPaginas = 1;


                this.manejarError(
                    error,
                    'No fue posible cargar los usuarios.'
                );

            } finally {

                this.cargando = false;

            }

        },


        /* =========================================================
           ROLES
        ========================================================= */

        async cargarRoles() {

            try {

                const response =
                    await axios.get(
                        '/api/admin/roles'
                    );


                const data =
                    response.data.data ||
                    response.data;


                if (Array.isArray(data)) {

                    this.roles = data;

                } else if (
                    data &&
                    Array.isArray(data.data)
                ) {

                    this.roles = data.data;

                } else {

                    this.roles = [];

                }


                console.log(
                    'Roles cargados:',
                    this.roles
                );


            } catch (error) {

                console.error(
                    'Error al cargar roles:',
                    error
                );


                this.roles = [];

            }

        },


        /* =========================================================
           DEPARTAMENTOS
        ========================================================= */

        async cargarDepartamentos() {

            try {

                const response =
                    await axios.get(
                        '/api/admin/departamentos'
                    );


                const data =
                    response.data.data ||
                    response.data;


                if (Array.isArray(data)) {

                    this.departamentos = data;

                } else if (
                    data &&
                    Array.isArray(data.data)
                ) {

                    this.departamentos = data.data;

                } else {

                    this.departamentos = [];

                }


                console.log(
                    'Departamentos cargados:',
                    this.departamentos
                );


            } catch (error) {

                console.error(
                    'Error al cargar departamentos:',
                    error
                );


                this.departamentos = [];

            }

        },


        /* =========================================================
           FILTROS
        ========================================================= */

        buscar() {

            this.paginaActual = 1;

            this.cargarUsuarios();

        },


        limpiarFiltros() {

            this.filtros = {

                search: '',

                role_id: '',

                departamento_id: '',

                activo: '',

                per_page: 10,

            };


            this.paginaActual = 1;

            this.cargarUsuarios();

        },


        cambiarPagina(pagina) {

            if (
                pagina === '...' ||
                pagina < 1 ||
                pagina > this.totalPaginas
            ) {

                return;

            }


            this.paginaActual = pagina;

            this.cargarUsuarios();

        },


        /* =========================================================
           NUEVO USUARIO
        ========================================================= */

        abrirFormulario() {

            this.error = null;

            this.mensaje = null;

            this.errores = {};

            this.modoEdicion = false;

            this.usuarioEditando = null;


            this.resetFormulario();


            /*
             * Esto nos ayuda a detectar inmediatamente
             * si el problema de los roles viene del backend.
             */

            if (!this.roles.length) {

                console.warn(
                    'No hay roles disponibles para seleccionar.'
                );

            }


            if (!this.departamentos.length) {

                console.warn(
                    'No hay departamentos disponibles para seleccionar.'
                );

            }


            this.mostrarFormulario = true;

        },


        /* =========================================================
           EDITAR USUARIO
        ========================================================= */

        editarUsuario(usuario) {

            if (!usuario) {

                return;

            }


            this.error = null;

            this.mensaje = null;

            this.errores = {};

            this.modoEdicion = true;

            this.usuarioEditando = usuario;


            this.form = {

                id: usuario.id,

                nombre:
                    usuario.nombre || '',

                apellido:
                    usuario.apellido || '',

                username:
                    usuario.username || '',

                email:
                    usuario.email || '',

                telefono:
                    usuario.telefono || '',

                extension:
                    usuario.extension || '',

                password: '',

                role_id:
                    usuario.role_id ||
                    (
                        usuario.role
                            ? usuario.role.id
                            : ''
                    ),

                departamento_id:
                    usuario.departamento_id ||
                    (
                        usuario.departamento
                            ? usuario.departamento.id
                            : ''
                    ),

                activo:
                    usuario.activo !== undefined
                        ? Boolean(usuario.activo)
                        : true,

            };


            this.mostrarFormulario = true;

        },


        /* =========================================================
           CERRAR FORMULARIO
        ========================================================= */

        cerrarFormulario() {

            if (this.guardando) {

                return;

            }


            this.mostrarFormulario = false;

            this.modoEdicion = false;

            this.usuarioEditando = null;


            this.resetFormulario();

        },


        /* =========================================================
           RESET
        ========================================================= */

        resetFormulario() {

            this.form = {

                id: null,

                nombre: '',

                apellido: '',

                username: '',

                email: '',

                telefono: '',

                extension: '',

                password: '',

                role_id: '',

                departamento_id: '',

                activo: true,

            };


            this.errores = {};

        },


        /* =========================================================
           CREAR / ACTUALIZAR
        ========================================================= */

        async guardarUsuario() {

            this.guardando = true;

            this.errores = {};

            this.error = null;


            const editando =
                this.modoEdicion;


            try {

                let response;


                /* =================================================
                   CREAR
                ================================================= */

                if (!editando) {

                    /*
                     * Creamos una copia para no modificar
                     * directamente el formulario.
                     */

                    const datos = {

                        nombre:
                            this.form.nombre,

                        apellido:
                            this.form.apellido,

                        username:
                            this.form.username,

                        email:
                            this.form.email,

                        telefono:
                            this.form.telefono,

                        extension:
                            this.form.extension,

                        password:
                            this.form.password,

                        role_id:
                            this.form.role_id,

                        departamento_id:
                            this.form.departamento_id,

                        activo:
                            this.form.activo,

                    };


                    response =
                        await axios.post(
                            '/api/admin/usuarios',
                            datos
                        );

                }


                /* =================================================
                   EDITAR
                ================================================= */

                else {

                    const datos = {

                        nombre:
                            this.form.nombre,

                        apellido:
                            this.form.apellido,

                        username:
                            this.form.username,

                        email:
                            this.form.email,

                        telefono:
                            this.form.telefono,

                        extension:
                            this.form.extension,

                        role_id:
                            this.form.role_id,

                        departamento_id:
                            this.form.departamento_id,

                        activo:
                            this.form.activo,

                    };


                    /*
                     * La contraseña solamente se manda
                     * si el usuario escribió una nueva.
                     */

                    if (
                        this.form.password &&
                        this.form.password.trim() !== ''
                    ) {

                        datos.password =
                            this.form.password;

                    }


                    /*
                     * IMPORTANTE:
                     * Esta ruta debe existir en api.php.
                     */

                    response =
                        await axios.put(
                            `/api/admin/usuarios/${this.form.id}`,
                            datos
                        );

                }


                this.mensaje =
                    response.data.message ||
                    (
                        editando
                            ? 'Usuario actualizado correctamente.'
                            : 'Usuario creado correctamente.'
                    );


                this.mostrarFormulario = false;

                this.modoEdicion = false;

                this.usuarioEditando = null;


                this.resetFormulario();


                this.paginaActual = 1;


                await this.cargarUsuarios();


                this.ocultarMensaje();


            } catch (error) {

                console.error(
                    'Error al guardar usuario:',
                    error
                );


                /*
                 * ERRORES DE VALIDACIÓN
                 */

                if (
                    error.response &&
                    error.response.status === 422
                ) {

                    this.errores =
                        error.response.data.errors ||
                        {};


                    this.error =
                        error.response.data.message ||
                        'Revisa los datos del formulario.';


                    /*
                     * Volvemos a abrir el modal
                     * para mostrar los errores.
                     */

                    this.mostrarFormulario = true;


                    return;

                }


                /*
                 * RESTO DE ERRORES
                 */

                this.manejarError(
                    error,
                    editando
                        ? 'No fue posible actualizar el usuario.'
                        : 'No fue posible crear el usuario.'
                );

            } finally {

                this.guardando = false;

            }

        },


        /* =========================================================
           CAMBIAR ESTADO
        ========================================================= */

        cambiarEstado(usuario) {

            if (!usuario) {

                return;

            }


            this.usuarioConfirmacion = usuario;

            this.accionConfirmacion = 'estado';

        },


        /* =========================================================
           CERRAR CONFIRMACIÓN
        ========================================================= */

        cerrarConfirmacion() {

            if (this.procesandoAccion) {

                return;

            }


            this.usuarioConfirmacion = null;

            this.accionConfirmacion = null;

        },


        /* =========================================================
           CONFIRMAR ACCIÓN
        ========================================================= */

        async confirmarAccion() {
             console.log('CONFIRMAR ACCION EJECUTADO');

            if (!this.usuarioConfirmacion) {

                return;

            }


            this.procesandoAccion = true;

            this.error = null;


            const usuario =
                this.usuarioConfirmacion;


            try {

                /*
                 * Una sola acción:
                 *
                 * activo -> desactivar
                 * inactivo -> activar
                 */

                const response =
                    await axios.patch(
                        `/api/admin/usuarios/${usuario.id}/estado`
                    );


                this.mensaje =
                    response.data.message ||
                    (
                        usuario.activo
                            ? 'Usuario desactivado correctamente.'
                            : 'Usuario activado correctamente.'
                    );


                this.cerrarConfirmacion();


                await this.cargarUsuarios();


                this.ocultarMensaje();


            } catch (error) {

                console.error(
                    'Error al cambiar estado del usuario:',
                    error
                );


                this.manejarError(
                    error,
                    'No fue posible cambiar el estado del usuario.'
                );

            } finally {

                this.procesandoAccion = false;

            }

        },


        /* =========================================================
           NOMBRE COMPLETO
        ========================================================= */

        nombreCompleto(usuario) {

            if (!usuario) {

                return 'Sin nombre';

            }


            return (
                `${usuario.nombre || ''} ` +
                `${usuario.apellido || ''}`
            ).trim();

        },


        /* =========================================================
           INICIALES
        ========================================================= */

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


        /* =========================================================
           NOMBRE DEL ROL
        ========================================================= */

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


        /* =========================================================
           MANEJAR ERROR
        ========================================================= */

        manejarError(
            error,
            mensajeDefault
        ) {

            if (
                error.response &&
                error.response.status === 401
            ) {

                this.error =
                    'Tu sesión ha expirado. Inicia sesión nuevamente.';

                return;

            }


            if (
                error.response &&
                error.response.status === 403
            ) {

                this.error =
                    'No tienes permisos para realizar esta acción.';

                return;

            }


            if (
                error.response &&
                error.response.status === 404
            ) {

                this.error =
                    'La ruta solicitada no existe en el servidor.';

                return;

            }


            this.error =
                (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) ||
                mensajeDefault;

        },


        /* =========================================================
           OCULTAR MENSAJE
        ========================================================= */

        ocultarMensaje() {

            setTimeout(() => {

                this.mensaje = null;

            }, 4000);

        },

    },

};

</script>


<style src="./styles/usuarios.css"></style>
```
