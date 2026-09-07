<template>
    <div class="llamada-form">

        <!-- ========================================================= -->
        <!-- ENCABEZADO                                                -->
        <!-- ========================================================= -->

        <div class="form-header">

            <div>
                <h2 class="form-title">
                    Registrar nueva llamada
                </h2>

                <p class="form-subtitle">
                    Completa la información de la llamada paso a paso.
                </p>
            </div>

            <button
                type="button"
                class="btn-close"
                @click="cancelar"
                :disabled="guardando"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>


        <!-- ========================================================= -->
        <!-- PASOS                                                     -->
        <!-- ========================================================= -->

        <div class="steps-container">

            <div
                v-for="paso in pasos"
                :key="paso.id"
                class="step"
                :class="{
                    'step-active': pasoActual === paso.id,
                    'step-completed': pasoCompletado(paso.id)
                }"
            >

                <div class="step-circle">

                    <i
                        v-if="pasoCompletado(paso.id)"
                        class="fas fa-check"
                    ></i>

                    <span v-else>
                        {{ paso.id }}
                    </span>

                </div>

                <div class="step-text">

                    <strong>
                        {{ paso.nombre }}
                    </strong>

                    <span>
                        {{ paso.descripcion }}
                    </span>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- ERROR GENERAL                                             -->
        <!-- ========================================================= -->

        <div
            v-if="error"
            class="alert-error"
        >

            <i class="fas fa-exclamation-circle"></i>

            <span>
                {{ error }}
            </span>

        </div>


        <!-- ========================================================= -->
        <!-- CONTENIDO                                                 -->
        <!-- ========================================================= -->

        <div class="form-content">


            <!-- ===================================================== -->
            <!-- PASO 1: CIUDADANO                                    -->
            <!-- ===================================================== -->

            <div v-if="pasoActual === 1">

                <div class="section-heading">

                    <div class="section-icon">
                        <i class="fas fa-user"></i>
                    </div>

                    <div>
                        <h3>
                            Datos del ciudadano
                        </h3>

                        <p>
                            Ingresa los datos básicos de la persona que realiza la llamada.
                        </p>
                    </div>

                </div>


                <div class="form-grid">


                    <!-- TELEFONO -->

                    <div class="form-group">

                        <label for="telefono">
                            Teléfono
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-phone"></i>

                            <input
                                id="telefono"
                                type="text"
                                v-model.trim="form.telefono"
                                placeholder="Número telefónico"
                                maxlength="20"
                                autocomplete="off"
                                :class="{
                                    'has-error': errores.telefono
                                }"
                            />

                        </div>

                        <span
                            v-if="errores.telefono"
                            class="error-text"
                        >
                            {{ errores.telefono }}
                        </span>

                    </div>


                    <!-- NOMBRE -->

                    <div class="form-group">

                        <label for="nombre">
                            Nombre
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-user"></i>

                            <input
                                id="nombre"
                                type="text"
                                v-model.trim="form.nombre"
                                placeholder="Nombre del ciudadano"
                                maxlength="255"
                                autocomplete="off"
                                :class="{
                                    'has-error': errores.nombre
                                }"
                            />

                        </div>

                        <span
                            v-if="errores.nombre"
                            class="error-text"
                        >
                            {{ errores.nombre }}
                        </span>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- PASO 2: INFORMACIÓN                                  -->
            <!-- ===================================================== -->

            <div v-if="pasoActual === 2">

                <div class="section-heading">

                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h3>
                            Información de la llamada
                        </h3>

                        <p>
                            Indica el motivo y la categoría de la llamada.
                        </p>
                    </div>

                </div>


                <!-- MOTIVO -->

                <div class="form-group">

                    <label for="motivo">
                        Motivo de la llamada
                    </label>

                    <textarea
                        id="motivo"
                        v-model.trim="form.motivo"
                        rows="5"
                        maxlength="1000"
                        placeholder="Describe brevemente el motivo de la llamada..."
                        :class="{
                            'has-error': errores.motivo
                        }"
                    ></textarea>

                    <div class="field-footer">

                        <span
                            v-if="errores.motivo"
                            class="error-text"
                        >
                            {{ errores.motivo }}
                        </span>

                        <span
                            v-else
                            class="error-placeholder"
                        ></span>

                        <span class="counter">
                            {{ form.motivo.length }}/1000
                        </span>

                    </div>

                </div>


                <!-- CATEGORIA -->

                <div class="form-group">

                    <label for="categoria">
                        Categoría
                    </label>

                    <div class="input-wrapper">

                        <i class="fas fa-tags"></i>

                        <select
                            id="categoria"
                            v-model="form.categoria"
                            :class="{
                                'has-error': errores.categoria
                            }"
                        >

                            <option value="">
                                Selecciona una categoría
                            </option>

                            <option value="informacion">
                                Información
                            </option>

                            <option value="queja">
                                Queja
                            </option>

                            <option value="tramite">
                                Trámite
                            </option>

                            <option value="soporte">
                                Soporte
                            </option>

                        </select>

                    </div>

                    <span
                        v-if="errores.categoria"
                        class="error-text"
                    >
                        {{ errores.categoria }}
                    </span>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- PASO 3: DEPARTAMENTO                                 -->
            <!-- ===================================================== -->

            <div v-if="pasoActual === 3">

                <div class="section-heading">

                    <div class="section-icon">
                        <i class="fas fa-building"></i>
                    </div>

                    <div>
                        <h3>
                            Departamento destino
                        </h3>

                        <p>
                            Selecciona el departamento al que será dirigida la llamada.
                        </p>
                    </div>

                </div>


                <!-- CARGANDO -->

                <div
                    v-if="cargandoDepartamentos"
                    class="loading-box"
                >

                    <i class="fas fa-spinner fa-spin"></i>

                    <span>
                        Cargando departamentos...
                    </span>

                </div>


                <!-- SELECT -->

                <div
                    v-else
                    class="form-group"
                >

                    <label for="departamento_id">
                        Departamento
                    </label>

                    <div class="input-wrapper">

                        <i class="fas fa-building"></i>

                        <select
                            id="departamento_id"
                            v-model="form.departamento_id"
                            :class="{
                                'has-error': errores.departamento_id
                            }"
                        >

                            <option value="">
                                Selecciona un departamento
                            </option>

                            <option
                                v-for="departamento in departamentos"
                                :key="departamento.id"
                                :value="departamento.id"
                            >
                                {{ departamento.nombre }}
                            </option>

                        </select>

                    </div>


                    <!-- ERROR -->

                    <span
                        v-if="errores.departamento_id"
                        class="error-text"
                    >
                        {{ errores.departamento_id }}
                    </span>


                    <!-- SIN DEPARTAMENTOS -->

                    <div
                        v-if="departamentos.length === 0"
                        class="empty-box"
                    >

                        <i class="fas fa-exclamation-triangle"></i>

                        <div>

                            <strong>
                                No hay departamentos disponibles
                            </strong>

                            <p>
                                No se pudieron cargar los departamentos.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- INFORMACIÓN DEL DEPARTAMENTO -->

                <div
                    v-if="departamentoSeleccionado"
                    class="department-card"
                >

                    <div class="department-icon">

                        <i class="fas fa-building"></i>

                    </div>

                    <div>

                        <strong>
                            {{ nombreDepartamento }}
                        </strong>

                        <p>
                            Departamento seleccionado como destino de la llamada.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- PASO 4: OBSERVACIONES                                -->
            <!-- ===================================================== -->

            <div v-if="pasoActual === 4">

                <div class="section-heading">

                    <div class="section-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                    <div>
                        <h3>
                            Observaciones
                        </h3>

                        <p>
                            Agrega cualquier información adicional antes de registrar la llamada.
                        </p>
                    </div>

                </div>


                <div class="form-group">

                    <label for="observaciones">
                        Observaciones
                    </label>

                    <textarea
                        id="observaciones"
                        v-model.trim="form.observaciones"
                        rows="6"
                        maxlength="2000"
                        placeholder="Escribe aquí cualquier observación adicional..."
                        :class="{
                            'has-error': errores.observaciones
                        }"
                    ></textarea>

                    <div class="field-footer">

                        <span
                            v-if="errores.observaciones"
                            class="error-text"
                        >
                            {{ errores.observaciones }}
                        </span>

                        <span
                            v-else
                            class="error-placeholder"
                        ></span>

                        <span class="counter">
                            {{ form.observaciones.length }}/2000
                        </span>

                    </div>

                </div>


                <!-- RESUMEN -->

                <div class="summary-card">

                    <div class="summary-title">

                        <i class="fas fa-file-alt"></i>

                        <span>
                            Resumen de la llamada
                        </span>

                    </div>


                    <div class="summary-grid">

                        <div class="summary-item">

                            <span class="summary-label">
                                Ciudadano
                            </span>

                            <span class="summary-value">
                                {{ form.nombre || 'No especificado' }}
                            </span>

                        </div>


                        <div class="summary-item">

                            <span class="summary-label">
                                Teléfono
                            </span>

                            <span class="summary-value">
                                {{ form.telefono || 'No especificado' }}
                            </span>

                        </div>


                        <div class="summary-item">

                            <span class="summary-label">
                                Categoría
                            </span>

                            <span class="summary-value">
                                {{ categoriaTexto || 'No especificada' }}
                            </span>

                        </div>


                        <div class="summary-item">

                            <span class="summary-label">
                                Departamento
                            </span>

                            <span class="summary-value">
                                {{ nombreDepartamento || 'No especificado' }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- FOOTER                                                    -->
        <!-- ========================================================= -->

        <div class="form-footer">

            <button
                v-if="pasoActual > 1"
                type="button"
                class="btn btn-secondary"
                @click="pasoAnterior"
                :disabled="guardando"
            >

                <i class="fas fa-arrow-left"></i>

                Anterior

            </button>


            <div class="footer-space"></div>


            <button
                type="button"
                class="btn btn-light"
                @click="cancelar"
                :disabled="guardando"
            >
                Cancelar
            </button>


            <button
                v-if="pasoActual < pasos.length"
                type="button"
                class="btn btn-primary"
                @click="siguientePaso"
                :disabled="guardando"
            >

                Siguiente

                <i class="fas fa-arrow-right"></i>

            </button>


            <button
                v-else
                type="button"
                class="btn btn-success"
                @click="guardar"
                :disabled="guardando"
            >

                <i
                    v-if="guardando"
                    class="fas fa-spinner fa-spin"
                ></i>

                <i
                    v-else
                    class="fas fa-save"
                ></i>

                {{ guardando ? 'Guardando...' : 'Registrar llamada' }}

            </button>

        </div>

    </div>
</template>


<script>

import axios from 'axios';


export default {

    name: 'LlamadaFormModal',


    data() {

        return {

            pasoActual: 1,


            pasos: [

                {
                    id: 1,
                    nombre: 'Ciudadano',
                    descripcion: 'Datos básicos'
                },

                {
                    id: 2,
                    nombre: 'Información',
                    descripcion: 'Motivo y categoría'
                },

                {
                    id: 3,
                    nombre: 'Departamento',
                    descripcion: 'Destino de la llamada'
                },

                {
                    id: 4,
                    nombre: 'Observaciones',
                    descripcion: 'Revisión y registro'
                }

            ],


            form: {

                telefono: '',

                nombre: '',

                motivo: '',

                categoria: '',

                departamento_id: '',

                observaciones: ''

            },


            departamentos: [],


            cargandoDepartamentos: false,


            guardando: false,


            errores: {},


            error: null

        };

    },


    computed: {


        /* ========================================================= */
        /* DEPARTAMENTO SELECCIONADO                                */
        /* ========================================================= */

        departamentoSeleccionado() {

            if (
                !this.form ||
                !this.form.departamento_id ||
                !Array.isArray(this.departamentos)
            ) {
                return null;
            }


            return this.departamentos.find(
                departamento => {

                    if (
                        !departamento ||
                        departamento.id === null ||
                        departamento.id === undefined
                    ) {
                        return false;
                    }


                    return (
                        String(departamento.id) ===
                        String(this.form.departamento_id)
                    );

                }
            ) || null;

        },


        /* ========================================================= */
        /* NOMBRE DEPARTAMENTO                                      */
        /* ========================================================= */

        nombreDepartamento() {

            if (
                !this.departamentoSeleccionado
            ) {
                return '';
            }


            return (
                this.departamentoSeleccionado.nombre ||
                ''
            );

        },


        /* ========================================================= */
        /* CATEGORIA TEXTO                                          */
        /* ========================================================= */

        categoriaTexto() {

            const categorias = {

                informacion: 'Información',

                queja: 'Queja',

                tramite: 'Trámite',

                soporte: 'Soporte'

            };


            return (
                categorias[this.form.categoria] ||
                ''
            );

        }

    },


    /* ============================================================= */
    /* CICLO DE VIDA                                                */
    /* ============================================================= */

    mounted() {

        console.log(
            'LlamadaFormModal montado correctamente'
        );


        this.cargarDepartamentos();

    },


    methods: {


        /* ========================================================= */
        /* CARGAR DEPARTAMENTOS                                     */
        /* ========================================================= */

        async cargarDepartamentos() {

            this.cargandoDepartamentos = true;

            this.error = null;


            try {

                console.log(
                    'Cargando departamentos...'
                );


                const response = await axios.get(
                    '/api/admin/departamentos'
                );


                console.log(
                    'Respuesta departamentos:',
                    response.data
                );


                const respuesta =
                    response &&
                    response.data
                        ? response.data
                        : null;


                let datos = [];


                /*
                 * Caso 1:
                 *
                 * [
                 *     { id: 1, nombre: 'RH' }
                 * ]
                 */

                if (Array.isArray(respuesta)) {

                    datos = respuesta;

                }


                /*
                 * Caso 2:
                 *
                 * {
                 *     data: [
                 *         { id: 1, nombre: 'RH' }
                 *     ]
                 * }
                 */

                else if (
                    respuesta &&
                    Array.isArray(respuesta.data)
                ) {

                    datos = respuesta.data;

                }


                /*
                 * Caso 3:
                 *
                 * {
                 *     data: {
                 *         data: [...]
                 *     }
                 * }
                 */

                else if (
                    respuesta &&
                    respuesta.data &&
                    Array.isArray(
                        respuesta.data.data
                    )
                ) {

                    datos =
                        respuesta.data.data;

                }


                /*
                 * Filtrar valores inválidos.
                 */

                this.departamentos =
                    datos.filter(
                        departamento => {

                            return (
                                departamento &&
                                typeof departamento === 'object' &&
                                departamento.id !== null &&
                                departamento.id !== undefined
                            );

                        }
                    );


                console.log(
                    'Departamentos cargados en LlamadaFormModal:',
                    this.departamentos
                );


                /*
                 * Si no se encontró ningún departamento,
                 * lo dejamos explícito en consola.
                 */

                if (
                    this.departamentos.length === 0
                ) {

                    console.warn(
                        'La API respondió correctamente, pero no se encontraron departamentos.'
                    );

                }

            } catch (error) {

                console.error(
                    'Error al cargar departamentos:',
                    error
                );


                this.departamentos = [];


                if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                }

                else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar los departamentos.';

                }

                else {

                    this.error =
                        'No fue posible cargar los departamentos.';

                }

            } finally {

                this.cargandoDepartamentos = false;

            }

        },


        /* ========================================================= */
        /* SIGUIENTE PASO                                           */
        /* ========================================================= */

        siguientePaso() {

            if (
                !this.validarPasoActual()
            ) {
                return;
            }


            if (
                this.pasoActual <
                this.pasos.length
            ) {

                this.pasoActual++;

                this.errores = {};

                this.error = null;

            }

        },


        /* ========================================================= */
        /* PASO ANTERIOR                                            */
        /* ========================================================= */

        pasoAnterior() {

            if (
                this.pasoActual > 1
            ) {

                this.pasoActual--;

                this.errores = {};

                this.error = null;

            }

        },


        /* ========================================================= */
        /* VALIDAR PASO                                             */
        /* ========================================================= */

        validarPasoActual() {

            this.errores = {};


            /*
             * PASO 1
             */

            if (
                this.pasoActual === 1
            ) {

                if (
                    !this.form.telefono
                ) {

                    this.$set(
                        this.errores,
                        'telefono',
                        'El teléfono es obligatorio.'
                    );

                }


                if (
                    !this.form.nombre
                ) {

                    this.$set(
                        this.errores,
                        'nombre',
                        'El nombre es obligatorio.'
                    );

                }

            }


            /*
             * PASO 2
             */

            if (
                this.pasoActual === 2
            ) {

                if (
                    !this.form.motivo
                ) {

                    this.$set(
                        this.errores,
                        'motivo',
                        'El motivo es obligatorio.'
                    );

                }


                if (
                    !this.form.categoria
                ) {

                    this.$set(
                        this.errores,
                        'categoria',
                        'Selecciona una categoría.'
                    );

                }

            }


            /*
             * PASO 3
             */

            if (
                this.pasoActual === 3
            ) {

                if (
                    !this.form.departamento_id
                ) {

                    this.$set(
                        this.errores,
                        'departamento_id',
                        'Selecciona un departamento.'
                    );

                }

            }


            return (
                Object.keys(
                    this.errores
                ).length === 0
            );

        },


        /* ========================================================= */
        /* PASO COMPLETADO                                          */
        /* ========================================================= */

        pasoCompletado(id) {

            if (
                id >= this.pasoActual
            ) {
                return false;
            }


            if (
                id === 1
            ) {

                return (
                    !!this.form.telefono &&
                    !!this.form.nombre
                );

            }


            if (
                id === 2
            ) {

                return (
                    !!this.form.motivo &&
                    !!this.form.categoria
                );

            }


            if (
                id === 3
            ) {

                return !!this.form.departamento_id;

            }


            return false;

        },


        /* ========================================================= */
        /* GUARDAR                                                   */
        /* ========================================================= */

        async guardar() {

            /*
             * Como estamos en el paso 4,
             * verificamos todos los pasos antes de enviar.
             */

            if (
                !this.validarTodosLosPasos()
            ) {

                return;

            }


            this.guardando = true;

            this.errores = {};

            this.error = null;


            try {

                const datos = {

                    telefono:
                        this.form.telefono,

                    nombre:
                        this.form.nombre,

                    motivo:
                        this.form.motivo,

                    categoria:
                        this.form.categoria,

                    departamento_id:
                        this.form.departamento_id,

                    observaciones:
                        this.form.observaciones ||
                        null

                };


                console.log(
                    'Datos que se enviarán:',
                    datos
                );


                const response =
                    await axios.post(
                        '/api/admin/llamadas',
                        datos
                    );


                console.log(
                    'Llamada guardada:',
                    response.data
                );


                /*
                 * IMPORTANTE:
                 * El index.vue escucha @guardado.
                 */

                this.$emit(
                    'guardado',
                    response.data
                );


                this.resetear();

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
                        'Revisa los datos ingresados.';

                }

                else if (
                    error.response &&
                    error.response.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                }

                else if (
                    error.response &&
                    error.response.status === 403
                ) {

                    this.error =
                        'No tienes permisos para registrar la llamada.';

                }

                else {

                    this.error =
                        'No fue posible guardar la llamada.';

                }

            } finally {

                this.guardando = false;

            }

        },


        /* ========================================================= */
        /* VALIDAR TODOS LOS PASOS                                  */
        /* ========================================================= */

        validarTodosLosPasos() {

            const errores = {};


            /*
             * Ciudadano
             */

            if (
                !this.form.telefono
            ) {

                errores.telefono =
                    'El teléfono es obligatorio.';

            }


            if (
                !this.form.nombre
            ) {

                errores.nombre =
                    'El nombre es obligatorio.';

            }


            /*
             * Información
             */

            if (
                !this.form.motivo
            ) {

                errores.motivo =
                    'El motivo es obligatorio.';

            }


            if (
                !this.form.categoria
            ) {

                errores.categoria =
                    'Selecciona una categoría.';

            }


            /*
             * Departamento
             */

            if (
                !this.form.departamento_id
            ) {

                errores.departamento_id =
                    'Selecciona un departamento.';

            }


            this.errores = errores;


            if (
                Object.keys(errores).length > 0
            ) {

                /*
                 * Llevar al usuario al primer
                 * paso que tenga error.
                 */

                if (
                    errores.telefono ||
                    errores.nombre
                ) {

                    this.pasoActual = 1;

                }

                else if (
                    errores.motivo ||
                    errores.categoria
                ) {

                    this.pasoActual = 2;

                }

                else if (
                    errores.departamento_id
                ) {

                    this.pasoActual = 3;

                }


                return false;

            }


            return true;

        },


        /* ========================================================= */
        /* CANCELAR                                                  */
        /* ========================================================= */

        cancelar() {

            if (
                this.guardando
            ) {
                return;
            }


            /*
             * IMPORTANTE:
             * El index.vue escucha @cancelar.
             */

            this.$emit(
                'cancelar'
            );

        },


        /* ========================================================= */
        /* RESETEAR                                                  */
        /* ========================================================= */

        resetear() {

            this.form = {

                telefono: '',

                nombre: '',

                motivo: '',

                categoria: '',

                departamento_id: '',

                observaciones: ''

            };


            this.pasoActual = 1;

            this.errores = {};

            this.error = null;

        }

    }

};

</script>


<style scoped>

.llamada-form {
    width: 100%;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    overflow: hidden;
}


/* ================================================================ */
/* HEADER                                                           */
/* ================================================================ */

.form-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 25px;
    border-bottom: 1px solid #e5e7eb;
}

.form-title {
    margin: 0;
    color: #111827;
    font-size: 21px;
    font-weight: 700;
}

.form-subtitle {
    margin: 5px 0 0;
    color: #6b7280;
    font-size: 13px;
}

.btn-close {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    background: #f3f4f6;
    color: #6b7280;
    cursor: pointer;
    transition: 0.2s;
}

.btn-close:hover {
    background: #e5e7eb;
    color: #111827;
}


/* ================================================================ */
/* STEPS                                                            */
/* ================================================================ */

.steps-container {
    display: flex;
    gap: 10px;
    padding: 18px 25px;
    border-bottom: 1px solid #e5e7eb;
    background: #fafafa;
}

.step {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
    opacity: 0.5;
}

.step-active,
.step-completed {
    opacity: 1;
}

.step-circle {
    width: 34px;
    height: 34px;
    min-width: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e5e7eb;
    color: #4b5563;
    font-size: 13px;
    font-weight: 700;
}

.step-active .step-circle {
    background: #2563eb;
    color: #ffffff;
}

.step-completed .step-circle {
    background: #16a34a;
    color: #ffffff;
}

.step-text {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.step-text strong {
    color: #374151;
    font-size: 12px;
}

.step-text span {
    margin-top: 2px;
    color: #9ca3af;
    font-size: 10px;
}


/* ================================================================ */
/* ERROR                                                            */
/* ================================================================ */

.alert-error {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 20px 25px 0;
    padding: 12px 14px;
    border: 1px solid #fecaca;
    border-radius: 8px;
    background: #fef2f2;
    color: #b91c1c;
    font-size: 13px;
}


/* ================================================================ */
/* CONTENT                                                          */
/* ================================================================ */

.form-content {
    padding: 25px;
}

.section-heading {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 25px;
}

.section-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #eff6ff;
    color: #2563eb;
}

.section-heading h3 {
    margin: 0;
    color: #111827;
    font-size: 17px;
    font-weight: 700;
}

.section-heading p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 12px;
}


/* ================================================================ */
/* FORMULARIOS                                                       */
/* ================================================================ */

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
}

.input-wrapper {
    position: relative;
}

.input-wrapper > i {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}

.input-wrapper input,
.input-wrapper select {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 13px 11px 38px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #ffffff;
    color: #111827;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: 0.2s;
}

.input-wrapper input:focus,
.input-wrapper select:focus,
textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
}

textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 12px 13px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #111827;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    resize: vertical;
    transition: 0.2s;
}

.has-error {
    border-color: #dc2626 !important;
}

.error-text {
    display: block;
    margin-top: 5px;
    color: #dc2626;
    font-size: 11px;
}

.error-placeholder {
    display: block;
}

.field-footer {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.counter {
    margin-left: auto;
    color: #9ca3af;
    font-size: 11px;
}


/* ================================================================ */
/* DEPARTAMENTOS                                                     */
/* ================================================================ */

.loading-box {
    min-height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border: 1px dashed #d1d5db;
    border-radius: 10px;
    color: #6b7280;
    font-size: 13px;
}

.loading-box i {
    color: #2563eb;
}

.empty-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 10px;
    padding: 13px;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    background: #fff7ed;
    color: #c2410c;
}

.empty-box > i {
    margin-top: 2px;
}

.empty-box strong {
    display: block;
    font-size: 12px;
}

.empty-box p {
    margin: 3px 0 0;
    color: #9a3412;
    font-size: 11px;
}

.department-card {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 15px;
    padding: 14px;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    background: #eff6ff;
}

.department-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #2563eb;
    color: #ffffff;
}

.department-card strong {
    display: block;
    color: #1e3a8a;
    font-size: 13px;
}

.department-card p {
    margin: 3px 0 0;
    color: #64748b;
    font-size: 11px;
}


/* ================================================================ */
/* RESUMEN                                                          */
/* ================================================================ */

.summary-card {
    margin-top: 25px;
    padding: 17px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
}

.summary-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    color: #111827;
    font-size: 14px;
    font-weight: 700;
}

.summary-title i {
    color: #2563eb;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.summary-item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.summary-label {
    color: #6b7280;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.summary-value {
    color: #111827;
    font-size: 13px;
    font-weight: 600;
}


/* ================================================================ */
/* FOOTER                                                           */
/* ================================================================ */

.form-footer {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 18px 25px;
    border-top: 1px solid #e5e7eb;
    background: #fafafa;
}

.footer-space {
    flex: 1;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn:disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.btn-primary {
    background: #2563eb;
    color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
    background: #1d4ed8;
}

.btn-success {
    background: #16a34a;
    color: #ffffff;
}

.btn-success:hover:not(:disabled) {
    background: #15803d;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover:not(:disabled) {
    background: #d1d5db;
}

.btn-light {
    border: 1px solid #d1d5db;
    background: #ffffff;
    color: #374151;
}

.btn-light:hover:not(:disabled) {
    background: #f9fafb;
}


/* ================================================================ */
/* RESPONSIVE                                                        */
/* ================================================================ */

@media (max-width: 700px) {

    .steps-container {
        overflow-x: auto;
    }

    .step {
        min-width: 150px;
    }

    .step-text span {
        display: none;
    }

    .form-grid,
    .summary-grid {
        grid-template-columns: 1fr;
    }

    .form-footer {
        flex-wrap: wrap;
    }

}

</style>