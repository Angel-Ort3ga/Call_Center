<template>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <!-- ============================================================
             ENCABEZADO
        ============================================================= -->

        <div class="px-6 py-5 border-b border-gray-200">

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="text-xl font-bold text-gray-800">
                        Nueva llamada
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Registra la información de la llamada recibida.
                    </p>

                </div>

                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
                    :disabled="guardando"
                    @click="$emit('cancelar')"
                >
                    ×
                </button>

            </div>

        </div>


        <!-- ============================================================
             CONTENIDO
        ============================================================= -->

        <div class="flex flex-col lg:flex-row">

            <!-- ========================================================
                 STEPS
            ========================================================= -->

            <div
                class="lg:w-64 bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-200 p-6"
            >

                <div class="mb-6">

                    <h3 class="font-semibold text-gray-800">
                        Registro de llamada
                    </h3>

                    <p class="text-xs text-gray-500 mt-1">
                        Completa todos los campos requeridos.
                    </p>

                </div>


                <!-- PASO 1 -->

                <div class="flex items-start gap-3 mb-6">

                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                        :class="claseStep(1)"
                    >

                        <span v-if="pasoCompletado(1)">
                            ✓
                        </span>

                        <span v-else>
                            1
                        </span>

                    </div>

                    <div>

                        <p class="font-medium text-sm text-gray-800">
                            Ciudadano
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Datos de contacto
                        </p>

                    </div>

                </div>


                <!-- PASO 2 -->

                <div class="flex items-start gap-3 mb-6">

                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                        :class="claseStep(2)"
                    >

                        <span v-if="pasoCompletado(2)">
                            ✓
                        </span>

                        <span v-else>
                            2
                        </span>

                    </div>

                    <div>

                        <p class="font-medium text-sm text-gray-800">
                            Información
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Motivo y categoría
                        </p>

                    </div>

                </div>


                <!-- PASO 3 -->

                <div class="flex items-start gap-3 mb-6">

                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                        :class="claseStep(3)"
                    >

                        <span v-if="pasoCompletado(3)">
                            ✓
                        </span>

                        <span v-else>
                            3
                        </span>

                    </div>

                    <div>

                        <p class="font-medium text-sm text-gray-800">
                            Departamento
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Destino de la llamada
                        </p>

                    </div>

                </div>


                <!-- PASO 4 -->

                <div class="flex items-start gap-3">

                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shrink-0"
                        :class="claseStep(4)"
                    >

                        <span v-if="pasoCompletado(4)">
                            ✓
                        </span>

                        <span v-else>
                            4
                        </span>

                    </div>

                    <div>

                        <p class="font-medium text-sm text-gray-800">
                            Observaciones
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Información adicional
                        </p>

                    </div>

                </div>

            </div>


            <!-- ========================================================
                 FORMULARIO
            ========================================================= -->

            <div class="flex-1 p-6">

                <!-- ERROR GENERAL -->

                <div
                    v-if="error"
                    class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg"
                >

                    <p class="text-sm font-medium text-red-700">
                        {{ error }}
                    </p>

                </div>


                <form
                    @submit.prevent="guardar"
                >

                    <!-- ==================================================
                         PASO 1
                    =================================================== -->

                    <div class="mb-8">

                        <div class="mb-5">

                            <h3 class="text-lg font-semibold text-gray-800">
                                Datos del ciudadano
                            </h3>

                            <p class="text-sm text-gray-500">
                                Información básica de la persona que realiza la llamada.
                            </p>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- TELÉFONO -->

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Teléfono

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>

                                <input
                                    v-model="form.telefono"
                                    type="text"
                                    maxlength="20"
                                    placeholder="Número de teléfono"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                                    :class="claseInput('telefono')"
                                    @input="limpiarError('telefono')"
                                >

                                <p
                                    v-if="errores.telefono"
                                    class="text-xs text-red-600 mt-1"
                                >
                                    {{ obtenerError('telefono') }}
                                </p>

                            </div>


                            <!-- NOMBRE -->

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Nombre

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>

                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    maxlength="150"
                                    placeholder="Nombre del ciudadano"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                                    :class="claseInput('nombre')"
                                    @input="limpiarError('nombre')"
                                >

                                <p
                                    v-if="errores.nombre"
                                    class="text-xs text-red-600 mt-1"
                                >
                                    {{ obtenerError('nombre') }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         PASO 2
                    =================================================== -->

                    <div class="mb-8">

                        <div class="mb-5">

                            <h3 class="text-lg font-semibold text-gray-800">
                                Información de la llamada
                            </h3>

                            <p class="text-sm text-gray-500">
                                Indica el motivo y categoría de la llamada.
                            </p>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- MOTIVO -->

                            <div class="md:col-span-2">

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Motivo

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>

                                <textarea
                                    v-model="form.motivo"
                                    rows="4"
                                    placeholder="Describe el motivo de la llamada..."
                                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 resize-none"
                                    :class="claseInput('motivo')"
                                    @input="limpiarError('motivo')"
                                ></textarea>

                                <p
                                    v-if="errores.motivo"
                                    class="text-xs text-red-600 mt-1"
                                >
                                    {{ obtenerError('motivo') }}
                                </p>

                            </div>


                            <!-- CATEGORÍA -->

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Categoría

                                    <span class="text-red-500">
                                        *
                                    </span>

                                </label>

                                <select
                                    v-model="form.categoria"
                                    class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                                    :class="claseInput('categoria')"
                                    @change="limpiarError('categoria')"
                                >

                                    <option value="">
                                        Selecciona una categoría
                                    </option>

                                    <option value="queja">
                                        Queja
                                    </option>

                                    <option value="informacion">
                                        Información
                                    </option>

                                    <option value="tramite">
                                        Trámite
                                    </option>

                                    <option value="soporte">
                                        Soporte
                                    </option>

                                </select>

                                <p
                                    v-if="errores.categoria"
                                    class="text-xs text-red-600 mt-1"
                                >
                                    {{ obtenerError('categoria') }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         PASO 3
                    =================================================== -->

                    <div class="mb-8">

                        <div class="mb-5">

                            <h3 class="text-lg font-semibold text-gray-800">
                                Departamento
                            </h3>

                            <p class="text-sm text-gray-500">
                                Selecciona el departamento al que corresponde la llamada.
                            </p>

                        </div>


                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">

                                Departamento

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>

                            <select
                                v-model="form.departamento_id"
                                class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2"
                                :class="claseInput('departamento_id')"
                                :disabled="cargandoDepartamentos"
                                @change="limpiarError('departamento_id')"
                            >

                                <option value="">

                                    {{
                                        cargandoDepartamentos
                                            ? 'Cargando departamentos...'
                                            : 'Selecciona un departamento'
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

                            <p
                                v-if="errores.departamento_id"
                                class="text-xs text-red-600 mt-1"
                            >
                                {{ obtenerError('departamento_id') }}
                            </p>

                        </div>

                    </div>


                    <!-- ==================================================
                         PASO 4
                    =================================================== -->

                    <div class="mb-8">

                        <div class="mb-5">

                            <h3 class="text-lg font-semibold text-gray-800">
                                Observaciones
                            </h3>

                            <p class="text-sm text-gray-500">
                                Agrega información adicional si es necesario.
                            </p>

                        </div>


                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">

                                Observaciones

                            </label>

                            <textarea
                                v-model="form.observaciones"
                                rows="4"
                                placeholder="Observaciones adicionales..."
                                class="w-full border rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 resize-none"
                                :class="claseInput('observaciones')"
                                @input="limpiarError('observaciones')"
                            ></textarea>

                            <p
                                v-if="errores.observaciones"
                                class="text-xs text-red-600 mt-1"
                            >
                                {{ obtenerError('observaciones') }}
                            </p>

                        </div>

                    </div>


                    <!-- ==================================================
                         BOTONES
                    =================================================== -->

                    <div
                        class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200"
                    >

                        <button
                            type="button"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                            :disabled="guardando"
                            @click="$emit('cancelar')"
                        >
                            Cancelar
                        </button>


                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition disabled:opacity-50"
                            :disabled="guardando"
                        >

                            <span v-if="guardando">
                                Guardando...
                            </span>

                            <span v-else>
                                Guardar llamada
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</template>


<script>

import axios from 'axios';


export default {

    name: 'LlamadaFormModal',


    data() {

        return {

            departamentos: [],

            cargandoDepartamentos: false,

            guardando: false,

            error: null,

            errores: {},


            form: {

                telefono: '',

                nombre: '',

                motivo: '',

                categoria: '',

                departamento_id: '',

                observaciones: '',

            },

        };

    },


    mounted() {

        this.cargarDepartamentos();

    },


    methods: {

        /* ============================================================
           DEPARTAMENTOS
        ============================================================= */

        async cargarDepartamentos() {

            this.cargandoDepartamentos = true;

            try {

                const response = await axios.get(
                    '/api/admin/departamentos'
                );

                const data =
                    response.data.data ||
                    response.data;


                if (Array.isArray(data)) {

                    this.departamentos = data;

                } else {

                    this.departamentos =
                        data.data || [];

                }

            } catch (error) {

                console.error(
                    'Error al cargar departamentos:',
                    error
                );

                this.error =
                    'No fue posible cargar los departamentos.';

            } finally {

                this.cargandoDepartamentos = false;

            }

        },


        /* ============================================================
           GUARDAR
        ============================================================= */

        async guardar() {

            this.guardando = true;

            this.error = null;

            this.errores = {};


            try {

                await axios.post(
                    '/api/admin/llamadas',
                    this.form
                );


                this.$emit('guardado');

                this.resetForm();

            } catch (error) {

                console.error(
                    'Error al guardar llamada:',
                    error
                );


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

                } else if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado.';

                } else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para registrar llamadas.';

                } else {

                    this.error =
                        'No fue posible guardar la llamada.';

                }

            } finally {

                this.guardando = false;

            }

        },


        /* ============================================================
           RESET
        ============================================================= */

        resetForm() {

            this.form = {

                telefono: '',

                nombre: '',

                motivo: '',

                categoria: '',

                departamento_id: '',

                observaciones: '',

            };


            this.errores = {};

            this.error = null;

        },


        /* ============================================================
           STEPS
        ============================================================= */

        pasoCompletado(paso) {

            if (paso === 1) {

                return (
                    !!this.form.telefono &&
                    !!this.form.nombre
                );

            }


            if (paso === 2) {

                return (
                    !!this.form.motivo &&
                    !!this.form.categoria
                );

            }


            if (paso === 3) {

                return !!this.form.departamento_id;

            }


            if (paso === 4) {

                return !!this.form.observaciones;

            }


            return false;

        },


        claseStep(paso) {

            if (this.pasoCompletado(paso)) {

                return 'bg-green-100 text-green-700';

            }


            return 'bg-gray-200 text-gray-600';

        },


        /* ============================================================
           ERRORES
        ============================================================= */

        limpiarError(campo) {

            if (this.errores[campo]) {

                const errores = {
                    ...this.errores,
                };

                delete errores[campo];

                this.errores = errores;

            }

        },


        obtenerError(campo) {

            const error = this.errores[campo];

            if (Array.isArray(error)) {

                return error[0];

            }

            return error || '';

        },


        claseInput(campo) {

            if (this.errores[campo]) {

                return 'border-red-300 focus:ring-red-200';

            }

            return 'border-gray-300 focus:ring-blue-200 focus:border-blue-500';

        },

    },

};

</script>