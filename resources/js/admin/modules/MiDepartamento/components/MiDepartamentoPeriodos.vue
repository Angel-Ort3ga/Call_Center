<template>
    <div class="period-card">

        <div class="period-header">
            <div>
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    Periodo de consulta
                </h2>

                <p class="section-subtitle">
                    Selecciona el periodo que deseas consultar.
                </p>
            </div>
        </div>

        <div class="period-buttons">

            <button
                type="button"
                class="period-button"
                :class="{ active: periodo === 'hoy' }"
                @click="seleccionarPeriodo('hoy')"
            >
                <i class="fas fa-calendar-day"></i>
                Hoy
            </button>

            <button
                type="button"
                class="period-button"
                :class="{ active: periodo === 'semana' }"
                @click="seleccionarPeriodo('semana')"
            >
                <i class="fas fa-calendar-week"></i>
                Esta semana
            </button>

            <button
                type="button"
                class="period-button"
                :class="{ active: periodo === 'mes' }"
                @click="seleccionarPeriodo('mes')"
            >
                <i class="fas fa-calendar"></i>
                Este mes
            </button>

            <button
                type="button"
                class="period-button"
                :class="{ active: periodo === 'todo' }"
                @click="seleccionarPeriodo('todo')"
            >
                <i class="fas fa-infinity"></i>
                Todo
            </button>

            <button
                type="button"
                class="period-button"
                :class="{ active: periodo === 'personalizado' }"
                @click="seleccionarPeriodo('personalizado')"
            >
                <i class="fas fa-calendar-alt"></i>
                Personalizado
            </button>

        </div>

        <!-- Fechas personalizadas -->

        <div
            v-if="periodo === 'personalizado'"
            class="custom-date-filters"
        >

            <div class="date-field">

                <label for="mi-departamento-fecha-desde">
                    Fecha desde
                </label>

                <input
                    id="mi-departamento-fecha-desde"
                    v-model="fechaDesde"
                    type="date"
                    class="date-input"
                >

            </div>

            <div class="date-field">

                <label for="mi-departamento-fecha-hasta">
                    Fecha hasta
                </label>

                <input
                    id="mi-departamento-fecha-hasta"
                    v-model="fechaHasta"
                    type="date"
                    class="date-input"
                >

            </div>

            <button
                type="button"
                class="apply-date-button"
                @click="aplicarPeriodo"
            >
                <i class="fas fa-check"></i>
                Aplicar
            </button>

        </div>

        <!-- Mensaje de error -->

        <div
            v-if="error"
            class="period-error"
        >
            <i class="fas fa-exclamation-circle"></i>
            {{ error }}
        </div>

    </div>
</template>

<script>
export default {
    name: 'MiDepartamentoPeriodos',

    props: {
        periodoInicial: {
            type: String,
            default: 'hoy',
        },

        fechaDesdeInicial: {
            type: String,
            default: '',
        },

        fechaHastaInicial: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            periodo: this.periodoInicial,
            fechaDesde: this.fechaDesdeInicial,
            fechaHasta: this.fechaHastaInicial,
            error: null,
        };
    },

    methods: {
        seleccionarPeriodo(periodo) {
            this.error = null;

            this.periodo = periodo;

            if (periodo === 'personalizado') {
                return;
            }

            this.$emit('cambio-periodo', {
                periodo: this.periodo,
                fechaDesde: '',
                fechaHasta: '',
            });
        },

        aplicarPeriodo() {
            this.error = null;

            if (!this.fechaDesde || !this.fechaHasta) {
                this.error = 'Debes seleccionar ambas fechas.';
                return;
            }

            if (this.fechaDesde > this.fechaHasta) {
                this.error = 'La fecha desde no puede ser mayor que la fecha hasta.';
                return;
            }

            this.$emit('cambio-periodo', {
                periodo: 'personalizado',
                fechaDesde: this.fechaDesde,
                fechaHasta: this.fechaHasta,
            });
        },
    },

    watch: {
        periodoInicial(valor) {
            if (valor) {
                this.periodo = valor;
            }
        },

        fechaDesdeInicial(valor) {
            this.fechaDesde = valor || '';
        },

        fechaHastaInicial(valor) {
            this.fechaHasta = valor || '';
        },
    },
};
</script>