```vue
<template>

    <div
        v-if="mostrar"
        class="modal-overlay"
        @click.self="cerrar"
    >

        <div class="modal-card">

            <!-- ===================================================== -->
            <!-- HEADER                                                -->
            <!-- ===================================================== -->

            <div class="modal-header">

                <div>

                    <h2>
                        {{
                            modoEdicion
                                ? 'Editar usuario'
                                : 'Nuevo usuario'
                        }}
                    </h2>

                    <p>
                        {{
                            modoEdicion
                                ? 'Actualiza la información del usuario.'
                                : 'Registra un nuevo usuario en el sistema.'
                        }}
                    </p>

                </div>


                <button
                    type="button"
                    class="modal-close"
                    :disabled="guardando"
                    @click="cerrar"
                >

                    <i class="close-icon"></i>

                </button>

            </div>


            <!-- ===================================================== -->
            <!-- FORMULARIO                                            -->
            <!-- ===================================================== -->

            <form
                class="user-form"
                @submit.prevent="guardar"
            >

                <!-- ================================================= -->
                <!-- NOMBRE                                             -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Nombre
                        <span>*</span>
                    </label>

                    <input
                        v-model="form.nombre"
                        type="text"
                        maxlength="100"
                        placeholder="Nombre"
                        autocomplete="given-name"
                    >

                    <small
                        v-if="errores.nombre"
                        class="field-error"
                    >
                        {{ errores.nombre }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- APELLIDO                                           -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Apellido
                        <span>*</span>
                    </label>

                    <input
                        v-model="form.apellido"
                        type="text"
                        maxlength="100"
                        placeholder="Apellido"
                        autocomplete="family-name"
                    >

                    <small
                        v-if="errores.apellido"
                        class="field-error"
                    >
                        {{ errores.apellido }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- USERNAME                                            -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Nombre de usuario
                        <span>*</span>
                    </label>

                    <input
                        v-model="form.username"
                        type="text"
                        maxlength="50"
                        placeholder="Ej. juan.perez"
                        autocomplete="username"
                    >

                    <small
                        v-if="errores.username"
                        class="field-error"
                    >
                        {{ errores.username }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- EMAIL                                               -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Correo electrónico
                        <span>*</span>
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        maxlength="255"
                        placeholder="correo@ejemplo.com"
                        autocomplete="email"
                    >

                    <small
                        v-if="errores.email"
                        class="field-error"
                    >
                        {{ errores.email }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- TELÉFONO                                           -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Teléfono
                    </label>

                    <input
                        v-model="form.telefono"
                        type="text"
                        maxlength="20"
                        placeholder="Teléfono"
                        autocomplete="tel"
                    >

                    <small
                        v-if="errores.telefono"
                        class="field-error"
                    >
                        {{ errores.telefono }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- EXTENSIÓN                                          -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Extensión
                    </label>

                    <input
                        v-model="form.extension"
                        type="text"
                        maxlength="10"
                        placeholder="Extensión"
                    >

                    <small
                        v-if="errores.extension"
                        class="field-error"
                    >
                        {{ errores.extension }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- ROL                                                 -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>
                        Rol
                        <span>*</span>
                    </label>

                    <select
                        v-model="form.role_id"
                    >

                        <option
                            disabled
                            value=""
                        >
                            Selecciona un rol
                        </option>

                        <option
                            v-for="rol in roles"
                            :key="rol.id"
                            :value="rol.id"
                        >
                            {{ nombreRol(rol) }}
                        </option>

                    </select>

                    <small
                        v-if="!roles.length"
                        class="field-help"
                    >
                        No hay roles disponibles.
                    </small>

                    <small
                        v-if="errores.role_id"
                        class="field-error"
                    >
                        {{ errores.role_id }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- DEPARTAMENTO                                       -->
                <!-- ================================================= -->

                <div class="form-group">

                    <label>

                        Departamento

                        <span v-if="esJefeDepartamento">
                            *
                        </span>

                        <span
                            v-else
                            class="optional-label"
                        >
                            (opcional)
                        </span>

                    </label>

                    <select
                        v-model="form.departamento_id"
                    >

                        <option value="">
                            {{
                                esJefeDepartamento
                                    ? 'Selecciona un departamento'
                                    : 'Sin departamento'
                            }}
                        </option>

                        <option
                            v-for="departamento in departamentos"
                            :key="departamento.id"
                            :value="departamento.id"
                        >
                            {{ departamento.nombre }}
                        </option>

                    </select>

                    <small
                        v-if="errores.departamento_id"
                        class="field-error"
                    >
                        {{ errores.departamento_id }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- CONTRASEÑA                                        -->
                <!-- ================================================= -->

                <div class="form-group form-group-full">

                    <label>

                        Contraseña

                        <span v-if="!modoEdicion">
                            *
                        </span>

                        <span
                            v-else
                            class="optional-label"
                        >
                            (opcional)
                        </span>

                    </label>


                    <div class="password-wrapper">

                        <input
                            v-model="form.password"
                            :type="
                                mostrarPassword
                                    ? 'text'
                                    : 'password'
                            "
                            :minlength="
                                modoEdicion
                                    ? null
                                    : 8
                            "
                            :placeholder="
                                modoEdicion
                                    ? 'Dejar vacía para conservar la actual'
                                    : 'Mínimo 8 caracteres'
                            "
                            :required="!modoEdicion"
                            autocomplete="new-password"
                        >


                        <button
                            type="button"
                            class="password-button"
                            @click="togglePassword"
                        >

                            <i
                                :class="
                                    mostrarPassword
                                        ? 'fas fa-eye-slash'
                                        : 'fas fa-eye'
                                "
                            ></i>

                        </button>

                    </div>


                    <small
                        v-if="modoEdicion"
                        class="field-help"
                    >
                        Deja este campo vacío si no deseas cambiar
                        la contraseña.
                    </small>


                    <small
                        v-if="errores.password"
                        class="field-error"
                    >
                        {{ errores.password }}
                    </small>

                </div>


                <!-- ================================================= -->
                <!-- ESTADO                                             -->
                <!-- ================================================= -->

                <div class="form-group form-group-full">

                    <label class="checkbox-label">

                        <input
                            v-model="form.activo"
                            type="checkbox"
                        >

                        <span>
                            Usuario activo
                        </span>

                    </label>

                </div>


                <!-- ================================================= -->
                <!-- BOTONES                                             -->
                <!-- ================================================= -->

                <div class="form-actions">

                    <button
                        type="button"
                        class="secondary-button"
                        :disabled="guardando"
                        @click="cerrar"
                    >

                        Cancelar

                    </button>


                    <button
                        type="submit"
                        class="primary-button"
                        :disabled="guardando"
                    >

                        <i
                            :class="
                                guardando
                                    ? 'fas fa-spinner fa-spin'
                                    : 'fas fa-save'
                            "
                        ></i>

                        {{
                            guardando
                                ? 'Guardando...'
                                : (
                                    modoEdicion
                                        ? 'Guardar cambios'
                                        : 'Guardar usuario'
                                )
                        }}

                    </button>

                </div>

            </form>

        </div>

    </div>

</template>


<script>

export default {

    name: 'UsuarioFormModal',


    props: {

        mostrar: {

            type: Boolean,

            default: false,

        },


        form: {

            type: Object,

            required: true,

        },


        roles: {

            type: Array,

            default: () => [],

        },


        departamentos: {

            type: Array,

            default: () => [],

        },


        errores: {

            type: Object,

            default: () => ({}),

        },


        guardando: {

            type: Boolean,

            default: false,

        },


        modoEdicion: {

            type: Boolean,

            default: false,

        },

    },


    data() {

        return {

            mostrarPassword: false,

        };

    },


    computed: {

        /**
         * Determinar si el rol seleccionado
         * es Jefe de departamento.
         */
        esJefeDepartamento() {

            if (!this.form || !this.form.role_id) {

                return false;

            }


            const rol =
                this.roles.find(
                    item =>
                        String(item.id) ===
                        String(this.form.role_id)
                );


            if (!rol) {

                return false;

            }


            return (
                rol.slug === 'jefe_departamento'
            );

        },

    },


    watch: {

        mostrar(nuevoValor) {

            if (nuevoValor) {

                this.mostrarPassword = false;

            }

        },


        'form.role_id'(nuevoRol) {

            /*
             * Si el usuario cambia a un rol que no necesita
             * departamento, limpiamos el departamento.
             */

            if (!nuevoRol) {

                return;

            }


            const rol =
                this.roles.find(
                    item =>
                        String(item.id) ===
                        String(nuevoRol)
                );


            if (
                rol &&
                rol.slug !== 'jefe_departamento'
            ) {

                this.form.departamento_id = '';

            }

        },

    },


    methods: {

        /* ========================================================== */
        /* CERRAR                                                     */
        /* ========================================================== */

        cerrar() {

            if (this.guardando) {

                return;

            }


            this.$emit('cerrar');

        },


        /* ========================================================== */
        /* GUARDAR                                                    */
        /* ========================================================== */

        guardar() {

            this.$emit('guardar');

        },


        /* ========================================================== */
        /* MOSTRAR / OCULTAR PASSWORD                                */
        /* ========================================================== */

        togglePassword() {

            this.mostrarPassword =
                !this.mostrarPassword;

        },


        /* ========================================================== */
        /* NOMBRE DEL ROL                                             */
        /* ========================================================== */

        nombreRol(rol) {

            if (!rol) {

                return 'Sin rol';

            }


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
                rol.nombre ||
                rol.name ||
                rol.slug ||
                'Sin rol'
            );

        },

    },

};

</script>
```
