<template>
    <div class="dashboard-page">

        <!-- ===================================================== -->
        <!-- HEADER                                                -->
        <!-- ===================================================== -->

        <header class="dashboard-header">

            <div>
                <h1 class="dashboard-title">
                    Dashboard
                </h1>

                <p class="dashboard-subtitle">
                    Resumen general del Call Center
                </p>
            </div>

            <button
                type="button"
                class="btn-refresh"
                :disabled="cargando"
                @click="cargarDashboard"
            >
                <i
                    class="fas fa-sync-alt"
                    :class="{ 'fa-spin': cargando }"
                ></i>

                {{ cargando ? 'Actualizando...' : 'Actualizar' }}
            </button>

        </header>


        <!-- ===================================================== -->
        <!-- SELECTOR DE PERIODO                                   -->
        <!-- ===================================================== -->

        <section class="dashboard-period-card">

            <div class="period-header">

                <div>
                    <h2>
                        Periodo de consulta
                    </h2>

                    <p>
                        Selecciona el periodo que deseas analizar.
                    </p>
                </div>

                <div
                    v-if="fechaInicio && fechaFin"
                    class="period-range"
                >
                    <i class="fas fa-calendar-alt"></i>

                    {{ fechaRangoTexto }}
                </div>

            </div>


            <div class="period-buttons">

                <button
                    type="button"
                    class="period-button"
                    :class="{ active: periodo === 'hoy' }"
                    @click="cambiarPeriodo('hoy')"
                >
                    <i class="fas fa-calendar-day"></i>
                    Hoy
                </button>

                <button
                    type="button"
                    class="period-button"
                    :class="{ active: periodo === 'semana' }"
                    @click="cambiarPeriodo('semana')"
                >
                    <i class="fas fa-calendar-week"></i>
                    Semana
                </button>

                <button
                    type="button"
                    class="period-button"
                    :class="{ active: periodo === 'mes' }"
                    @click="cambiarPeriodo('mes')"
                >
                    <i class="fas fa-calendar-alt"></i>
                    Mes
                </button>

                <button
                    type="button"
                    class="period-button"
                    :class="{ active: periodo === 'personalizado' }"
                    @click="mostrarPersonalizado"
                >
                    <i class="fas fa-calendar-plus"></i>
                    Personalizado
                </button>

            </div>


            <!-- ================================================= -->
            <!-- FECHAS PERSONALIZADAS                            -->
            <!-- ================================================= -->

            <div
                v-if="mostrarFechas"
                class="custom-date-panel"
            >

                <div class="date-field">

                    <label for="fechaInicio">
                        Fecha inicial
                    </label>

                    <input
                        id="fechaInicio"
                        v-model="fechaInicioPersonalizada"
                        type="date"
                    >

                </div>


                <div class="date-field">

                    <label for="fechaFin">
                        Fecha final
                    </label>

                    <input
                        id="fechaFin"
                        v-model="fechaFinPersonalizada"
                        type="date"
                    >

                </div>


                <button
                    type="button"
                    class="btn-apply-date"
                    :disabled="!fechasValidas || cargando"
                    @click="aplicarPeriodoPersonalizado"
                >
                    <i class="fas fa-check"></i>
                    Aplicar
                </button>

            </div>

        </section>


        <!-- ===================================================== -->
        <!-- ERROR                                                 -->
        <!-- ===================================================== -->

        <div
            v-if="error"
            class="dashboard-error"
        >
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="error-content">
                <strong>
                    No fue posible cargar el Dashboard
                </strong>

                <p>
                    {{ error }}
                </p>
            </div>

            <button
                type="button"
                @click="cargarDashboard"
            >
                Reintentar
            </button>
        </div>


        <!-- ===================================================== -->
        <!-- LOADING                                               -->
        <!-- ===================================================== -->

        <div
            v-if="cargando && !tieneDatos"
            class="dashboard-loading"
        >
            <i class="fas fa-spinner fa-spin"></i>

            <span>
                Cargando información...
            </span>
        </div>


        <template v-else>

            <!-- ================================================= -->
            <!-- RESUMEN DE LLAMADAS                              -->
            <!-- ================================================= -->

            <section class="dashboard-section">

                <div class="section-heading">

                    <div>
                        <h2>
                            Resumen de llamadas
                        </h2>

                        <p>
                            Actividad registrada durante el periodo seleccionado.
                        </p>
                    </div>

                </div>


                <div class="stats-grid">

                    <!-- TOTAL -->

                    <article class="stat-card stat-total">

                        <div class="stat-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>

                        <div class="stat-info">

                            <span class="stat-label">
                                Total de llamadas
                            </span>

                            <strong class="stat-value">
                                {{ llamadas.total }}
                            </strong>

                        </div>

                    </article>


                    <!-- EN PROCESO -->

                    <article class="stat-card stat-process">

                        <div class="stat-icon">
                            <i class="fas fa-phone-volume"></i>
                        </div>

                        <div class="stat-info">

                            <span class="stat-label">
                                En proceso
                            </span>

                            <strong class="stat-value">
                                {{ llamadas.en_proceso }}
                            </strong>

                        </div>

                    </article>


                    <!-- TRANSFERIDAS -->

                    <article class="stat-card stat-transfer">

                        <div class="stat-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>

                        <div class="stat-info">

                            <span class="stat-label">
                                Transferidas
                            </span>

                            <strong class="stat-value">
                                {{ llamadas.transferidas }}
                            </strong>

                        </div>

                    </article>


                    <!-- FINALIZADAS -->

                    <article class="stat-card stat-finished">

                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>

                        <div class="stat-info">

                            <span class="stat-label">
                                Finalizadas
                            </span>

                            <strong class="stat-value">
                                {{ llamadas.finalizadas }}
                            </strong>

                        </div>

                    </article>

                </div>

            </section>


            <!-- ================================================= -->
            <!-- USUARIOS / DEPARTAMENTOS                         -->
            <!-- ================================================= -->

            <section class="summary-grid">

                <!-- USUARIOS -->

                <article class="summary-card">

                    <div class="summary-card-header">

                        <div>
                            <span class="summary-kicker">
                                Personal
                            </span>

                            <h2>
                                Usuarios
                            </h2>
                        </div>

                        <div class="summary-icon users-icon">
                            <i class="fas fa-users"></i>
                        </div>

                    </div>


                    <div class="summary-values">

                        <div class="summary-value">

                            <span>
                                Total
                            </span>

                            <strong>
                                {{ usuarios.total }}
                            </strong>

                        </div>


                        <div class="summary-value">

                            <span>
                                Activos
                            </span>

                            <strong class="value-active">
                                {{ usuarios.activos }}
                            </strong>

                        </div>


                        <div class="summary-value">

                            <span>
                                Inactivos
                            </span>

                            <strong class="value-inactive">
                                {{ usuarios.inactivos }}
                            </strong>

                        </div>

                    </div>

                </article>


                <!-- DEPARTAMENTOS -->

                <article class="summary-card">

                    <div class="summary-card-header">

                        <div>
                            <span class="summary-kicker">
                                Organización
                            </span>

                            <h2>
                                Departamentos
                            </h2>
                        </div>

                        <div class="summary-icon departments-icon">
                            <i class="fas fa-building"></i>
                        </div>

                    </div>


                    <div class="summary-values">

                        <div class="summary-value">

                            <span>
                                Total
                            </span>

                            <strong>
                                {{ departamentos.total }}
                            </strong>

                        </div>


                        <div class="summary-value">

                            <span>
                                Activos
                            </span>

                            <strong class="value-active">
                                {{ departamentos.activos }}
                            </strong>

                        </div>


                        <div class="summary-value">

                            <span>
                                Inactivos
                            </span>

                            <strong class="value-inactive">
                                {{ departamentos.inactivos }}
                            </strong>

                        </div>

                    </div>

                </article>

            </section>


            <!-- ================================================= -->
            <!-- DEPARTAMENTOS / CATEGORÍAS                       -->
            <!-- ================================================= -->

            <section class="charts-grid">

                <!-- POR DEPARTAMENTO -->

                <article class="chart-card">

                    <div class="chart-header">

                        <div>
                            <span class="chart-kicker">
                                Distribución
                            </span>

                            <h2>
                                Llamadas por departamento
                            </h2>
                        </div>

                        <span class="chart-total">
                            {{ llamadas.total }}
                        </span>

                    </div>


                    <div
                        v-if="estadisticas.por_departamento.length"
                        class="horizontal-chart"
                    >

                        <div
                            v-for="item in estadisticas.por_departamento"
                            :key="item.departamento_id"
                            class="horizontal-row"
                        >

                            <div class="horizontal-label">

                                <span>
                                    {{ item.departamento || 'Sin departamento' }}
                                </span>

                                <strong>
                                    {{ item.total }}
                                </strong>

                            </div>

                            <div class="horizontal-track">

                                <div
                                    class="horizontal-fill department-fill"
                                    :style="{
                                        width: calcularPorcentaje(
                                            item.total,
                                            totalPorDepartamento
                                        ) + '%'
                                    }"
                                ></div>

                            </div>

                        </div>

                    </div>


                    <div
                        v-else
                        class="empty-chart"
                    >
                        <i class="fas fa-chart-bar"></i>

                        <span>
                            No hay llamadas en este periodo.
                        </span>
                    </div>

                </article>


                <!-- POR CATEGORÍA -->

                <article class="chart-card">

                    <div class="chart-header">

                        <div>
                            <span class="chart-kicker">
                                Clasificación
                            </span>

                            <h2>
                                Llamadas por categoría
                            </h2>
                        </div>

                        <span class="chart-total">
                            {{ totalPorCategoria }}
                        </span>

                    </div>


                    <div
                        v-if="estadisticas.por_categoria.length"
                        class="horizontal-chart"
                    >

                        <div
                            v-for="item in estadisticas.por_categoria"
                            :key="item.categoria"
                            class="horizontal-row"
                        >

                            <div class="horizontal-label">

                                <span>
                                    {{ categoriaTexto(item.categoria) }}
                                </span>

                                <strong>
                                    {{ item.total }}
                                </strong>

                            </div>

                            <div class="horizontal-track">

                                <div
                                    class="horizontal-fill category-fill"
                                    :style="{
                                        width: calcularPorcentaje(
                                            item.total,
                                            totalPorCategoria
                                        ) + '%'
                                    }"
                                ></div>

                            </div>

                        </div>

                    </div>


                    <div
                        v-else
                        class="empty-chart"
                    >
                        <i class="fas fa-chart-pie"></i>

                        <span>
                            No hay categorías registradas.
                        </span>
                    </div>

                </article>

            </section>


            <!-- ================================================= -->
            <!-- EVOLUCIÓN POR DÍA                                 -->
            <!-- ================================================= -->

            <section class="chart-card evolution-card">

                <div class="chart-header">

                    <div>
                        <span class="chart-kicker">
                            Tendencia
                        </span>

                        <h2>
                            Evolución de llamadas
                        </h2>

                        <p class="chart-description">
                            Cantidad de llamadas registradas por día.
                        </p>
                    </div>

                    <span class="chart-total">
                        {{ llamadas.total }}
                    </span>

                </div>


                <div
                    v-if="estadisticas.por_dia.length"
                    class="evolution-chart"
                >

                    <div
                        v-for="item in estadisticas.por_dia"
                        :key="item.fecha"
                        class="evolution-column"
                    >

                        <div class="evolution-value">
                            {{ item.total }}
                        </div>

                        <div class="evolution-bar-container">

                            <div
                                class="evolution-bar"
                                :style="{
                                    height: calcularAlturaDia(item.total) + '%'
                                }"
                            ></div>

                        </div>

                        <div class="evolution-label">
                            {{ formatearFechaCorta(item.fecha) }}
                        </div>

                    </div>

                </div>


                <div
                    v-else
                    class="empty-chart evolution-empty"
                >
                    <i class="fas fa-chart-line"></i>

                    <span>
                        No hay información suficiente para mostrar la evolución.
                    </span>
                </div>

            </section>


            <!-- ================================================= -->
            <!-- POR HORA                                          -->
            <!-- ================================================= -->

            <section class="chart-card hourly-card">

                <div class="chart-header">

                    <div>
                        <span class="chart-kicker">
                            Horarios
                        </span>

                        <h2>
                            Llamadas por hora
                        </h2>

                        <p class="chart-description">
                            Distribución de llamadas según la hora de registro.
                        </p>
                    </div>

                    <span class="chart-total">
                        {{ totalPorHora }}
                    </span>

                </div>


                <div
                    v-if="estadisticas.por_hora.length"
                    class="hourly-chart"
                >

                    <div
                        v-for="item in estadisticas.por_hora"
                        :key="item.hora"
                        class="hour-column"
                    >

                        <div class="hour-value">
                            {{ item.total }}
                        </div>

                        <div class="hour-bar-container">

                            <div
                                class="hour-bar"
                                :style="{
                                    height: calcularAlturaHora(item.total) + '%'
                                }"
                            ></div>

                        </div>

                        <div class="hour-label">
                            {{ item.hora }}
                        </div>

                    </div>

                </div>


                <div
                    v-else
                    class="empty-chart"
                >
                    <i class="fas fa-clock"></i>

                    <span>
                        No hay llamadas registradas en este periodo.
                    </span>
                </div>

            </section>


            <!-- ================================================= -->
            <!-- PIE                                                -->
            <!-- ================================================= -->

            <footer class="dashboard-footer">

                <div>
                    <i class="fas fa-calendar-check"></i>

                    <span>
                        Periodo:
                    </span>

                    <strong>
                        {{ periodoTexto }}
                    </strong>
                </div>


                <div v-if="usuario.nombre">

                    <i class="fas fa-user"></i>

                    <span>
                        Usuario:
                    </span>

                    <strong>
                        {{ usuario.nombre }}
                    </strong>

                </div>

            </footer>

        </template>

    </div>
</template>


<script>

import './styles/dashboard.css';

export default {

    name: 'Dashboard',

    data() {

        return {

            /*
            |--------------------------------------------------------------------------
            | ESTADO GENERAL
            |--------------------------------------------------------------------------
            */

            cargando: false,

            error: null,


            /*
            |--------------------------------------------------------------------------
            | PERIODO
            |--------------------------------------------------------------------------
            */

            periodo: 'hoy',

            mostrarFechas: false,

            fechaInicio: null,

            fechaFin: null,

            fechaInicioPersonalizada: '',

            fechaFinPersonalizada: '',


            /*
            |--------------------------------------------------------------------------
            | USUARIO
            |--------------------------------------------------------------------------
            */

            usuario: {

                id: null,

                nombre: '',

                departamento_id: null,
            },


            /*
            |--------------------------------------------------------------------------
            | USUARIOS
            |--------------------------------------------------------------------------
            */

            usuarios: {

                total: 0,

                activos: 0,

                inactivos: 0,
            },


            /*
            |--------------------------------------------------------------------------
            | DEPARTAMENTOS
            |--------------------------------------------------------------------------
            */

            departamentos: {

                total: 0,

                activos: 0,

                inactivos: 0,
            },


            /*
            |--------------------------------------------------------------------------
            | LLAMADAS
            |--------------------------------------------------------------------------
            */

            llamadas: {

                total: 0,

                en_proceso: 0,

                transferidas: 0,

                finalizadas: 0,
            },


            /*
            |--------------------------------------------------------------------------
            | ESTADÍSTICAS
            |--------------------------------------------------------------------------
            */

            estadisticas: {

                por_departamento: [],

                por_categoria: [],

                por_hora: [],

                por_dia: [],
            },
        };
    },


    computed: {

        /*
        |--------------------------------------------------------------------------
        | ¿TENEMOS DATOS?
        |--------------------------------------------------------------------------
        */

        tieneDatos() {

            return (
                this.llamadas.total > 0 ||
                this.usuarios.total > 0 ||
                this.departamentos.total > 0
            );
        },


        /*
        |--------------------------------------------------------------------------
        | TOTAL POR DEPARTAMENTO
        |--------------------------------------------------------------------------
        */

        totalPorDepartamento() {

            return this.estadisticas.por_departamento.reduce(
                (total, item) => {

                    return total + Number(item.total || 0);

                },
                0
            );
        },


        /*
        |--------------------------------------------------------------------------
        | TOTAL POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        totalPorCategoria() {

            return this.estadisticas.por_categoria.reduce(
                (total, item) => {

                    return total + Number(item.total || 0);

                },
                0
            );
        },


        /*
        |--------------------------------------------------------------------------
        | TOTAL POR HORA
        |--------------------------------------------------------------------------
        */

        totalPorHora() {

            return this.estadisticas.por_hora.reduce(
                (total, item) => {

                    return total + Number(item.total || 0);

                },
                0
            );
        },


        /*
        |--------------------------------------------------------------------------
        | MÁXIMO POR DÍA
        |--------------------------------------------------------------------------
        */

        maxLlamadasDia() {

            if (!this.estadisticas.por_dia.length) {
                return 0;
            }

            return Math.max(
                ...this.estadisticas.por_dia.map(
                    item => Number(item.total || 0)
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | MÁXIMO POR HORA
        |--------------------------------------------------------------------------
        */

        maxLlamadasHora() {

            if (!this.estadisticas.por_hora.length) {
                return 0;
            }

            return Math.max(
                ...this.estadisticas.por_hora.map(
                    item => Number(item.total || 0)
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | FECHAS VÁLIDAS
        |--------------------------------------------------------------------------
        */

        fechasValidas() {

            if (
                !this.fechaInicioPersonalizada ||
                !this.fechaFinPersonalizada
            ) {
                return false;
            }

            return (
                this.fechaInicioPersonalizada <=
                this.fechaFinPersonalizada
            );
        },


        /*
        |--------------------------------------------------------------------------
        | TEXTO DEL PERIODO
        |--------------------------------------------------------------------------
        */

        periodoTexto() {

            if (this.periodo === 'hoy') {
                return 'Hoy';
            }

            if (this.periodo === 'semana') {
                return 'Esta semana';
            }

            if (this.periodo === 'mes') {
                return 'Este mes';
            }

            if (this.periodo === 'personalizado') {

                if (
                    this.fechaInicio &&
                    this.fechaFin
                ) {

                    return (
                        this.formatearFecha(
                            this.fechaInicio
                        ) +
                        ' - ' +
                        this.formatearFecha(
                            this.fechaFin
                        )
                    );
                }

                return 'Periodo personalizado';
            }

            return 'Periodo seleccionado';
        },


        /*
        |--------------------------------------------------------------------------
        | RANGO
        |--------------------------------------------------------------------------
        */

        fechaRangoTexto() {

            if (
                !this.fechaInicio ||
                !this.fechaFin
            ) {
                return '';
            }

            const inicio = this.formatearFecha(
                this.fechaInicio
            );

            const fin = this.formatearFecha(
                this.fechaFin
            );

            if (inicio === fin) {
                return inicio;
            }

            return `${inicio} - ${fin}`;
        },
    },


    mounted() {

        this.cargarDashboard();
    },


    methods: {

        /*
        |--------------------------------------------------------------------------
        | CARGAR DASHBOARD
        |--------------------------------------------------------------------------
        */

        async cargarDashboard() {

            this.cargando = true;

            this.error = null;

            try {

                const params = {
                    periodo: this.periodo,
                };


                if (
                    this.periodo === 'personalizado'
                ) {

                    params.fecha_inicio =
                        this.fechaInicioPersonalizada;

                    params.fecha_fin =
                        this.fechaFinPersonalizada;
                }


                const response = await axios.get(
                    '/api/admin/dashboard',
                    {
                        params,
                    }
                );


                const data =
                    response.data?.data || {};


                /*
                |--------------------------------------------------------------------------
                | USUARIO
                |--------------------------------------------------------------------------
                */

                this.usuario = {

                    id:
                        data.usuario?.id ??
                        null,

                    nombre:
                        data.usuario?.nombre ??
                        '',

                    departamento_id:
                        data.usuario?.departamento_id ??
                        null,
                };


                /*
                |--------------------------------------------------------------------------
                | USUARIOS
                |--------------------------------------------------------------------------
                */

                this.usuarios = {

                    total: Number(
                        data.usuarios?.total || 0
                    ),

                    activos: Number(
                        data.usuarios?.activos || 0
                    ),

                    inactivos: Number(
                        data.usuarios?.inactivos || 0
                    ),
                };


                /*
                |--------------------------------------------------------------------------
                | DEPARTAMENTOS
                |--------------------------------------------------------------------------
                */

                this.departamentos = {

                    total: Number(
                        data.departamentos?.total || 0
                    ),

                    activos: Number(
                        data.departamentos?.activos || 0
                    ),

                    inactivos: Number(
                        data.departamentos?.inactivos || 0
                    ),
                };


                /*
                |--------------------------------------------------------------------------
                | LLAMADAS
                |--------------------------------------------------------------------------
                */

                this.llamadas = {

                    total: Number(
                        data.llamadas?.total || 0
                    ),

                    en_proceso: Number(
                        data.llamadas?.en_proceso || 0
                    ),

                    transferidas: Number(
                        data.llamadas?.transferidas || 0
                    ),

                    finalizadas: Number(
                        data.llamadas?.finalizadas || 0
                    ),
                };


                /*
                |--------------------------------------------------------------------------
                | ESTADÍSTICAS
                |--------------------------------------------------------------------------
                */

                this.estadisticas = {

                    por_departamento:
                        Array.isArray(
                            data.estadisticas?.por_departamento
                        )
                            ? data.estadisticas.por_departamento
                            : [],

                    por_categoria:
                        Array.isArray(
                            data.estadisticas?.por_categoria
                        )
                            ? data.estadisticas.por_categoria
                            : [],

                    por_hora:
                        Array.isArray(
                            data.estadisticas?.por_hora
                        )
                            ? data.estadisticas.por_hora
                            : [],

                    por_dia:
                        Array.isArray(
                            data.estadisticas?.por_dia
                        )
                            ? data.estadisticas.por_dia
                            : [],
                };


                /*
                |--------------------------------------------------------------------------
                | FECHAS DEVUELTAS POR BACKEND
                |--------------------------------------------------------------------------
                */

                this.fechaInicio =
                    data.fecha_inicio || null;

                this.fechaFin =
                    data.fecha_fin || null;


            } catch (error) {

                console.error(
                    'Error cargando Dashboard:',
                    error
                );


                if (
                    error.response?.status === 401
                ) {

                    this.error =
                        'Tu sesión ha expirado. Inicia sesión nuevamente.';

                } else if (
                    error.response?.status === 403
                ) {

                    this.error =
                        'No tienes permisos para consultar el Dashboard.';

                } else if (
                    error.response?.status === 422
                ) {

                    this.error =
                        error.response?.data?.message ||
                        'Las fechas seleccionadas no son válidas.';

                } else {

                    this.error =
                        'No fue posible cargar la información del Dashboard.';
                }

            } finally {

                this.cargando = false;
            }
        },


        /*
        |--------------------------------------------------------------------------
        | CAMBIAR PERIODO
        |--------------------------------------------------------------------------
        */

        cambiarPeriodo(periodo) {

            this.periodo = periodo;

            this.mostrarFechas = false;

            this.cargarDashboard();
        },


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        mostrarPersonalizado() {

            this.periodo = 'personalizado';

            this.mostrarFechas = true;


            if (
                !this.fechaInicioPersonalizada
            ) {

                this.fechaInicioPersonalizada =
                    this.obtenerFechaActual();
            }


            if (
                !this.fechaFinPersonalizada
            ) {

                this.fechaFinPersonalizada =
                    this.obtenerFechaActual();
            }
        },


        /*
        |--------------------------------------------------------------------------
        | APLICAR PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        aplicarPeriodoPersonalizado() {

            if (!this.fechasValidas) {
                return;
            }

            this.periodo = 'personalizado';

            this.mostrarFechas = true;

            this.cargarDashboard();
        },


        /*
        |--------------------------------------------------------------------------
        | PORCENTAJE
        |--------------------------------------------------------------------------
        */

        calcularPorcentaje(valor, total) {

            const numero = Number(valor || 0);

            const totalNumero = Number(total || 0);

            if (
                totalNumero <= 0 ||
                numero <= 0
            ) {
                return 0;
            }

            return Math.min(
                100,
                Math.round(
                    (numero / totalNumero) * 100
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | ALTURA DÍA
        |--------------------------------------------------------------------------
        */

        calcularAlturaDia(valor) {

            const numero = Number(valor || 0);

            if (
                !this.maxLlamadasDia ||
                numero <= 0
            ) {
                return 0;
            }

            return Math.max(
                8,
                Math.round(
                    (numero / this.maxLlamadasDia) * 100
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | ALTURA HORA
        |--------------------------------------------------------------------------
        */

        calcularAlturaHora(valor) {

            const numero = Number(valor || 0);

            if (
                !this.maxLlamadasHora ||
                numero <= 0
            ) {
                return 0;
            }

            return Math.max(
                8,
                Math.round(
                    (numero / this.maxLlamadasHora) * 100
                )
            );
        },


        /*
        |--------------------------------------------------------------------------
        | CATEGORÍA
        |--------------------------------------------------------------------------
        */

        categoriaTexto(categoria) {

            const categorias = {

                informacion: 'Información',

                queja: 'Queja',

                tramite: 'Trámite',

                soporte: 'Soporte',
            };

            return (
                categorias[categoria] ||
                categoria ||
                'Sin categoría'
            );
        },


        /*
        |--------------------------------------------------------------------------
        | FECHA ACTUAL
        |--------------------------------------------------------------------------
        */

        obtenerFechaActual() {

            const fecha = new Date();

            const year =
                fecha.getFullYear();

            const month =
                String(
                    fecha.getMonth() + 1
                ).padStart(2, '0');

            const day =
                String(
                    fecha.getDate()
                ).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },


        /*
        |--------------------------------------------------------------------------
        | FORMATEAR FECHA
        |--------------------------------------------------------------------------
        */

        formatearFecha(fecha) {

            if (!fecha) {
                return '';
            }

            const partes =
                String(fecha).split('-');

            if (partes.length !== 3) {
                return fecha;
            }

            return `${partes[2]}/${partes[1]}/${partes[0]}`;
        },


        /*
        |--------------------------------------------------------------------------
        | FECHA CORTA PARA GRÁFICA
        |--------------------------------------------------------------------------
        */

        formatearFechaCorta(fecha) {

            if (!fecha) {
                return '';
            }

            const partes =
                String(fecha).split('-');

            if (partes.length !== 3) {
                return fecha;
            }

            return `${partes[2]}/${partes[1]}`;
        },
    },
};

</script>