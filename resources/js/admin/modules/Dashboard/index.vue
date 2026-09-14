<template>
    <div class="dashboard-page">

    <!-- ========================================================= -->
    <!-- HEADER                                                    -->
    <!-- ========================================================= -->

    <div class="dashboard-header">

        <div>
            <h1 class="dashboard-title">
                Dashboard
            </h1>

            <p class="dashboard-subtitle">
                Resumen general de llamadas del Call Center
            </p>
        </div>

        <button
            class="refresh-button"
            :disabled="cargando"
            @click="cargarDashboard"
        >
            <i
                class="fas fa-sync-alt"
                :class="{ spinning: cargando }"
            ></i>

            {{ cargando ? 'Actualizando...' : 'Actualizar' }}
        </button>

    </div>


    <!-- ========================================================= -->
    <!-- FILTRO DE FECHA                                           -->
    <!-- ========================================================= -->

    <div class="date-card">

        <div class="date-content">

            <div>
                <label
                    for="fecha"
                    class="date-label"
                >
                    <i class="fas fa-calendar-alt"></i>
                    Fecha de consulta
                </label>

                <p class="date-description">
                    Consulta las estadísticas correspondientes a una fecha.
                </p>
            </div>

            <input
                id="fecha"
                v-model="fecha"
                type="date"
                class="date-input"
                :disabled="cargando"
                @change="cargarDashboard"
            >

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- ERROR                                                     -->
    <!-- ========================================================= -->

    <div
        v-if="error"
        class="error-message"
    >
        <i class="fas fa-exclamation-circle"></i>

        <span>{{ error }}</span>

        <button
            type="button"
            @click="cargarDashboard"
        >
            Reintentar
        </button>
    </div>


    <!-- ========================================================= -->
    <!-- ESTADÍSTICAS DE LLAMADAS                                  -->
    <!-- ========================================================= -->

    <section class="section">

        <div class="section-header">
            <div>
                <h2 class="section-title">
                    <i class="fas fa-phone-alt"></i>
                    Resumen de llamadas
                </h2>

                <p class="section-subtitle">
                    Actividad registrada para la fecha seleccionada.
                </p>
            </div>
        </div>


        <div class="stats-grid">

            <!-- Total -->

            <div class="stat-card">

                <div class="stat-icon total">
                    <i class="fas fa-phone"></i>
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Total de llamadas
                    </span>

                    <strong class="stat-value">
                        {{ llamadas.total }}
                    </strong>

                </div>

            </div>


            <!-- En proceso -->

            <div class="stat-card">

                <div class="stat-icon process">
                    <i class="fas fa-phone-volume"></i>
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        En proceso
                    </span>

                    <strong class="stat-value">
                        {{ llamadas.en_proceso }}
                    </strong>

                </div>

            </div>


            <!-- Transferidas -->

            <div class="stat-card">

                <div class="stat-icon transferred">
                    <i class="fas fa-exchange-alt"></i>
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Transferidas
                    </span>

                    <strong class="stat-value">
                        {{ llamadas.transferidas }}
                    </strong>

                </div>

            </div>


            <!-- Finalizadas -->

            <div class="stat-card">

                <div class="stat-icon finished">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Finalizadas
                    </span>

                    <strong class="stat-value">
                        {{ llamadas.finalizadas }}
                    </strong>

                </div>

            </div>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- USUARIOS Y DEPARTAMENTOS                                  -->
    <!-- ========================================================= -->

    <section class="summary-grid">

        <!-- Usuarios -->

        <div class="summary-card">

            <div class="summary-header">

                <div>
                    <h2 class="summary-title">
                        Usuarios
                    </h2>

                    <p class="summary-subtitle">
                        Estado actual de los usuarios registrados.
                    </p>
                </div>

                <div class="summary-icon users">
                    <i class="fas fa-users"></i>
                </div>

            </div>


            <div class="summary-values">

                <div class="summary-item">
                    <span>Total</span>

                    <strong>
                        {{ usuarios.total }}
                    </strong>
                </div>

                <div class="summary-item active">
                    <span>Activos</span>

                    <strong>
                        {{ usuarios.activos }}
                    </strong>
                </div>

                <div class="summary-item inactive">
                    <span>Inactivos</span>

                    <strong>
                        {{ usuarios.inactivos }}
                    </strong>
                </div>

            </div>

        </div>


        <!-- Departamentos -->

        <div class="summary-card">

            <div class="summary-header">

                <div>
                    <h2 class="summary-title">
                        Departamentos
                    </h2>

                    <p class="summary-subtitle">
                        Estado actual de los departamentos.
                    </p>
                </div>

                <div class="summary-icon departments">
                    <i class="fas fa-building"></i>
                </div>

            </div>


            <div class="summary-values">

                <div class="summary-item">
                    <span>Total</span>

                    <strong>
                        {{ departamentos.total }}
                    </strong>
                </div>

                <div class="summary-item active">
                    <span>Activos</span>

                    <strong>
                        {{ departamentos.activos }}
                    </strong>
                </div>

            </div>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- ANALYTICS                                                 -->
    <!-- ========================================================= -->

    <section class="analytics-grid">

        <!-- ===================================================== -->
        <!-- POR DEPARTAMENTO                                      -->
        <!-- ===================================================== -->

        <div class="analytics-card">

            <div class="analytics-header">

                <div>
                    <h2 class="analytics-title">
                        Llamadas por departamento
                    </h2>

                    <p class="analytics-subtitle">
                        Distribución de llamadas entre departamentos.
                    </p>
                </div>

                <i class="fas fa-building analytics-header-icon"></i>

            </div>


            <div
                v-if="estadisticas.por_departamento.length === 0"
                class="analytics-empty"
            >
                <i class="fas fa-chart-bar"></i>

                <p>
                    No hay llamadas registradas para esta fecha.
                </p>
            </div>


            <div
                v-else
                class="bar-list"
            >

                <div
                    v-for="item in estadisticas.por_departamento"
                    :key="item.departamento_id"
                    class="bar-item"
                >

                    <div class="bar-info">

                        <span class="bar-name">
                            {{ item.departamento || 'Sin departamento' }}
                        </span>

                        <strong class="bar-total">
                            {{ item.total }}
                        </strong>

                    </div>


                    <div class="bar-track">

                        <div
                            class="bar-fill department"
                            :style="{
                                width:
                                    calcularPorcentaje(
                                        item.total,
                                        totalPorDepartamento
                                    ) + '%'
                            }"
                        ></div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- POR CATEGORÍA                                         -->
        <!-- ===================================================== -->

        <div class="analytics-card">

            <div class="analytics-header">

                <div>
                    <h2 class="analytics-title">
                        Llamadas por categoría
                    </h2>

                    <p class="analytics-subtitle">
                        Distribución según categoría de atención.
                    </p>
                </div>

                <i class="fas fa-tags analytics-header-icon"></i>

            </div>


            <div
                v-if="estadisticas.por_categoria.length === 0"
                class="analytics-empty"
            >
                <i class="fas fa-chart-pie"></i>

                <p>
                    No hay llamadas registradas para esta fecha.
                </p>
            </div>


            <div
                v-else
                class="bar-list"
            >

                <div
                    v-for="item in estadisticas.por_categoria"
                    :key="item.categoria"
                    class="bar-item"
                >

                    <div class="bar-info">

                        <span class="bar-name">
                            {{ categoriaTexto(item.categoria) }}
                        </span>

                        <strong class="bar-total">
                            {{ item.total }}
                        </strong>

                    </div>


                    <div class="bar-track">

                        <div
                            class="bar-fill category"
                            :style="{
                                width:
                                    calcularPorcentaje(
                                        item.total,
                                        totalPorCategoria
                                    ) + '%'
                            }"
                        ></div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- LLAMADAS POR HORA                                         -->
    <!-- ========================================================= -->

    <section class="hours-card">

        <div class="analytics-header">

            <div>
                <h2 class="analytics-title">
                    Llamadas por hora
                </h2>

                <p class="analytics-subtitle">
                    Distribución de llamadas durante el día.
                </p>
            </div>

            <i class="fas fa-clock analytics-header-icon"></i>

        </div>


        <div
            v-if="estadisticas.por_hora.length === 0"
            class="analytics-empty"
        >
            <i class="fas fa-chart-column"></i>

            <p>
                No hay llamadas registradas para esta fecha.
            </p>
        </div>


        <div
            v-else
            class="hours-chart"
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
                            height:
                                calcularAlturaHora(item.total) + 'px'
                        }"
                    ></div>

                </div>

                <span class="hour-label">
                    {{ item.hora }}
                </span>

            </div>

        </div>

    </section>


    <!-- ========================================================= -->
    <!-- INFORMACIÓN DE CONSULTA                                   -->
    <!-- ========================================================= -->

    <div class="dashboard-footer">

        <span>
            <i class="fas fa-calendar-check"></i>

            Fecha consultada:
            <strong>{{ fechaFormateada }}</strong>
        </span>

        <span
            v-if="usuario.nombre"
        >
            <i class="fas fa-user"></i>

            {{ usuario.nombre }}
        </span>

    </div>

</div>


</template>

<script>

import axios from 'axios';

export default {

    name: 'Dashboard',

    data() {

        return {

            fecha: this.obtenerFechaActual(),

            cargando: false,

            error: null,


            usuario: {
                id: null,
                nombre: '',
                departamento_id: null,
            },


            usuarios: {
                total: 0,
                activos: 0,
                inactivos: 0,
            },


            departamentos: {
                total: 0,
                activos: 0,
            },


            llamadas: {
                total: 0,
                en_proceso: 0,
                transferidas: 0,
                finalizadas: 0,
            },


            estadisticas: {

                por_departamento: [],

                por_categoria: [],

                por_hora: [],

            },

        };

    },


    computed: {

        totalPorDepartamento() {

            return this.estadisticas.por_departamento
                .reduce(
                    (total, item) =>
                        total + Number(item.total || 0),
                    0
                );

        },


        totalPorCategoria() {

            return this.estadisticas.por_categoria
                .reduce(
                    (total, item) =>
                        total + Number(item.total || 0),
                    0
                );

        },


        maxLlamadasHora() {

            if (
                !this.estadisticas.por_hora.length
            ) {
                return 0;
            }

            return Math.max(
                ...this.estadisticas.por_hora.map(
                    item =>
                        Number(item.total || 0)
                )
            );

        },


        fechaFormateada() {

            if (!this.fecha) {
                return '';
            }

            const partes =
                this.fecha.split('-');

            if (partes.length !== 3) {
                return this.fecha;
            }

            return `${partes[2]}/${partes[1]}/${partes[0]}`;

        },

    },


    mounted() {

        this.cargarDashboard();

    },


    methods: {

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


        async cargarDashboard() {

            this.cargando = true;

            this.error = null;

            try {

                const response =
                    await axios.get(
                        '/api/admin/dashboard',
                        {
                            params: {
                                fecha: this.fecha,
                            },
                        }
                    );


                const data =
                    response.data.data;


                this.usuario = {

                    id:
                        data.usuario?.id || null,

                    nombre:
                        data.usuario?.nombre || '',

                    departamento_id:
                        data.usuario?.departamento_id || null,

                };


                this.usuarios = {

                    total:
                        Number(
                            data.usuarios?.total || 0
                        ),

                    activos:
                        Number(
                            data.usuarios?.activos || 0
                        ),

                    inactivos:
                        Number(
                            data.usuarios?.inactivos || 0
                        ),

                };


                this.departamentos = {

                    total:
                        Number(
                            data.departamentos?.total || 0
                        ),

                    activos:
                        Number(
                            data.departamentos?.activos || 0
                        ),

                };


                this.llamadas = {

                    total:
                        Number(
                            data.llamadas?.total || 0
                        ),

                    en_proceso:
                        Number(
                            data.llamadas?.en_proceso || 0
                        ),

                    transferidas:
                        Number(
                            data.llamadas?.transferidas || 0
                        ),

                    finalizadas:
                        Number(
                            data.llamadas?.finalizadas || 0
                        ),

                };


                this.estadisticas = {

                    por_departamento:
                        data.estadisticas
                            ?.por_departamento || [],

                    por_categoria:
                        data.estadisticas
                            ?.por_categoria || [],

                    por_hora:
                        data.estadisticas
                            ?.por_hora || [],

                };


            } catch (error) {

                console.error(
                    'Error al cargar el dashboard:',
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
                        'No tienes permisos para consultar el dashboard.';

                } else {

                    this.error =
                        'No fue posible cargar las estadísticas del dashboard.';

                }

            } finally {

                this.cargando = false;

            }

        },


        calcularPorcentaje(
            valor,
            total
        ) {

            const numero =
                Number(valor || 0);

            const totalNumero =
                Number(total || 0);

            if (
                totalNumero <= 0 ||
                numero <= 0
            ) {
                return 0;
            }

            return Math.max(
                3,
                Math.round(
                    (numero / totalNumero) * 100
                )
            );

        },


        calcularAlturaHora(valor) {

            const numero =
                Number(valor || 0);

            const maximo =
                Number(
                    this.maxLlamadasHora || 0
                );

            if (
                numero <= 0 ||
                maximo <= 0
            ) {
                return 4;
            }

            const altura =
                (numero / maximo) * 120;

            return Math.max(
                8,
                Math.round(altura)
            );

        },


        categoriaTexto(
            categoria
        ) {

            const categorias = {

                informacion:
                    'Información',

                queja:
                    'Queja',

                tramite:
                    'Trámite',

                soporte:
                    'Soporte',

            };

            return (
                categorias[categoria] ||
                categoria ||
                'Sin categoría'
            );

        },

    },

};

</script>

<style scoped>

.dashboard-page {

    min-height: 100vh;

    padding: 30px;

    background: #f5f7fa;

}


/* =========================================================
   HEADER
   ========================================================= */

.dashboard-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;

}

.dashboard-title {

    margin: 0;

    color: #1f2937;

    font-size: 28px;

    font-weight: 700;

}

.dashboard-subtitle {

    margin: 6px 0 0;

    color: #6b7280;

    font-size: 14px;

}

.refresh-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-height: 40px;

    padding: 0 16px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    cursor: pointer;

    font-size: 13px;

    font-weight: 600;

    transition:
        background 0.2s ease,
        border-color 0.2s ease;

}

.refresh-button:hover:not(:disabled) {

    background: #f9fafb;

    border-color: #9ca3af;

}

.refresh-button:disabled {

    opacity: 0.6;

    cursor: not-allowed;

}


/* =========================================================
   DATE
   ========================================================= */

.date-card {

    margin-bottom: 25px;

    padding: 20px 22px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

}

.date-content {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 20px;

}

.date-label {

    display: block;

    margin-bottom: 6px;

    color: #374151;

    font-size: 13px;

    font-weight: 700;

}

.date-label i {

    margin-right: 6px;

    color: #2563eb;

}

.date-description {

    margin: 0;

    color: #9ca3af;

    font-size: 12px;

}

.date-input {

    width: 180px;

    height: 40px;

    padding: 0 11px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    outline: none;

}

.date-input:focus {

    border-color: #2563eb;

}

.date-input:disabled {

    background: #f9fafb;

    cursor: not-allowed;

}


/* =========================================================
   ERROR
   ========================================================= */

.error-message {

    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 25px;

    padding: 13px 16px;

    background: #fef2f2;

    border: 1px solid #fecaca;

    border-radius: 8px;

    color: #b91c1c;

    font-size: 14px;

}

.error-message button {

    margin-left: auto;

    padding: 6px 12px;

    border: none;

    border-radius: 6px;

    background: #dc2626;

    color: #ffffff;

    cursor: pointer;

    font-size: 12px;

    font-weight: 600;

}

.error-message button:hover {

    background: #b91c1c;

}


/* =========================================================
   SECTION
   ========================================================= */

.section {

    margin-bottom: 25px;

}

.section-header {

    margin-bottom: 15px;

}

.section-title {

    display: flex;

    align-items: center;

    gap: 8px;

    margin: 0;

    color: #1f2937;

    font-size: 18px;

    font-weight: 700;

}

.section-title i {

    color: #2563eb;

}

.section-subtitle {

    margin: 5px 0 0;

    color: #6b7280;

    font-size: 12px;

}


/* =========================================================
   STATS
   ========================================================= */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 18px;

}

.stat-card {

    display: flex;

    align-items: center;

    gap: 15px;

    min-width: 0;

    padding: 20px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

}

.stat-icon {

    width: 46px;

    height: 46px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 10px;

    font-size: 17px;

}

.stat-icon.total {

    background: #e0f2fe;

    color: #0284c7;

}

.stat-icon.process {

    background: #fef3c7;

    color: #d97706;

}

.stat-icon.transferred {

    background: #ede9fe;

    color: #7c3aed;

}

.stat-icon.finished {

    background: #dcfce7;

    color: #16a34a;

}

.stat-content {

    min-width: 0;

}

.stat-label {

    display: block;

    margin-bottom: 5px;

    color: #6b7280;

    font-size: 12px;

}

.stat-value {

    display: block;

    color: #111827;

    font-size: 26px;

    font-weight: 700;

}


/* =========================================================
   SUMMARY
   ========================================================= */

.summary-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 25px;

}

.summary-card {

    padding: 22px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

}

.summary-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

}

.summary-title {

    margin: 0;

    color: #1f2937;

    font-size: 17px;

    font-weight: 700;

}

.summary-subtitle {

    margin: 5px 0 0;

    color: #6b7280;

    font-size: 12px;

}

.summary-icon {

    width: 40px;

    height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

}

.summary-icon.users {

    background: #eff6ff;

    color: #2563eb;

}

.summary-icon.departments {

    background: #f3f4f6;

    color: #4b5563;

}

.summary-values {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 12px;

    margin-top: 22px;

}

.summary-item {

    display: flex;

    flex-direction: column;

    gap: 5px;

    padding: 13px;

    border-radius: 8px;

    background: #f9fafb;

}

.summary-item span {

    color: #6b7280;

    font-size: 11px;

    font-weight: 600;

}

.summary-item strong {

    color: #111827;

    font-size: 20px;

}

.summary-item.active {

    background: #f0fdf4;

}

.summary-item.active strong {

    color: #16a34a;

}

.summary-item.inactive {

    background: #fef2f2;

}

.summary-item.inactive strong {

    color: #dc2626;

}


/* =========================================================
   ANALYTICS
   ========================================================= */

.analytics-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 18px;

}

.analytics-card {

    min-width: 0;

    padding: 22px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

}

.analytics-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 20px;

}

.analytics-title {

    margin: 0;

    color: #1f2937;

    font-size: 17px;

    font-weight: 700;

}

.analytics-subtitle {

    margin: 5px 0 0;

    color: #6b7280;

    font-size: 12px;

}

.analytics-header-icon {

    color: #2563eb;

    font-size: 18px;

}

.analytics-empty {

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    min-height: 150px;

    text-align: center;

    color: #9ca3af;

}

.analytics-empty i {

    margin-bottom: 10px;

    font-size: 24px;

}

.analytics-empty p {

    margin: 0;

    font-size: 13px;

}


/* =========================================================
   BARS
   ========================================================= */

.bar-list {

    display: flex;

    flex-direction: column;

    gap: 16px;

}

.bar-item {

    width: 100%;

}

.bar-info {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    margin-bottom: 7px;

}

.bar-name {

    overflow: hidden;

    min-width: 0;

    color: #374151;

    font-size: 13px;

    font-weight: 600;

    text-overflow: ellipsis;

    white-space: nowrap;

}

.bar-total {

    flex-shrink: 0;

    color: #111827;

    font-size: 13px;

}

.bar-track {

    width: 100%;

    height: 9px;

    overflow: hidden;

    border-radius: 999px;

    background: #eef2f7;

}

.bar-fill {

    height: 100%;

    min-width: 3px;

    border-radius: 999px;

    transition: width 0.3s ease;

}

.bar-fill.department {

    background: #2563eb;

}

.bar-fill.category {

    background: #7c3aed;

}


/* =========================================================
   HOURS
   ========================================================= */

.hours-card {

    margin-bottom: 25px;

    padding: 22px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

}

.hours-chart {

    display: flex;

    align-items: flex-end;

    gap: 8px;

    min-height: 190px;

    padding: 20px 8px 0;

    border-bottom: 1px solid #e5e7eb;

}

.hour-column {

    display: flex;

    flex: 1;

    flex-direction: column;

    align-items: center;

    justify-content: flex-end;

    min-width: 0;

    height: 170px;

}

.hour-value {

    margin-bottom: 6px;

    color: #374151;

    font-size: 10px;

    font-weight: 700;

}

.hour-bar-container {

    display: flex;

    align-items: flex-end;

    justify-content: center;

    width: 100%;

    height: 120px;

}

.hour-bar {

    width: min(30px, 75%);

    min-height: 4px;

    border-radius: 6px 6px 0 0;

    background: #2563eb;

    transition: height 0.3s ease;

}

.hour-label {

    margin-top: 8px;

    color: #6b7280;

    font-size: 10px;

    font-weight: 600;

}


/* =========================================================
   FOOTER
   ========================================================= */

.dashboard-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 15px 18px;

    background: #ffffff;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    color: #6b7280;

    font-size: 12px;

}

.dashboard-footer strong {

    color: #374151;

}

.dashboard-footer i {

    margin-right: 5px;

    color: #2563eb;

}


/* =========================================================
   ANIMATION
   ========================================================= */

.spinning {

    animation:
        spin 1s linear infinite;

}

@keyframes spin {

    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }

}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 1100px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

    .analytics-grid {

        grid-template-columns: 1fr;

    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 700px) {

    .dashboard-page {

        padding: 20px;

    }

    .dashboard-header {

        flex-direction: column;

        align-items: flex-start;

    }

    .refresh-button {

        width: 100%;

    }

    .date-content {

        flex-direction: column;

        align-items: stretch;

    }

    .date-input {

        width: 100%;

        box-sizing: border-box;

    }

    .stats-grid {

        grid-template-columns: 1fr;

        gap: 12px;

    }

    .summary-grid {

        grid-template-columns: 1fr;

        gap: 12px;

    }

    .summary-values {

        grid-template-columns:
            repeat(3, 1fr);

    }

    .analytics-card,
    .hours-card {

        padding: 18px;

    }

    .hours-chart {

        gap: 4px;

        padding-left: 0;

        padding-right: 0;

        overflow-x: auto;

    }

    .hour-column {

        min-width: 32px;

    }

    .dashboard-footer {

        flex-direction: column;

        align-items: flex-start;

    }

}

</style>
