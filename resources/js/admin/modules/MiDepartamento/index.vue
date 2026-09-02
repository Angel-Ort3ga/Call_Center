```vue
<template>
    <div class="departamento-page">

        <!-- ENCABEZADO -->
        <MiDepartamentoHeader
            @actualizar="actualizar"
        />

        <!-- RESUMEN -->
        <MiDepartamentoResumen
            :resumen="resumen"
        />

        <!-- LLAMADAS -->
        <div class="calls-card">

            <div class="calls-header">

                <div>
                    <h2>
                        Llamadas del departamento
                    </h2>

                    <p>
                        Registro de llamadas recibidas por el departamento.
                    </p>
                </div>

                <button
                    type="button"
                    class="refresh-button"
                    @click="actualizar"
                >
                    <i class="fas fa-sync-alt"></i>

                    Actualizar
                </button>

            </div>

            <!-- FILTROS -->
            <MiDepartamentoFiltros
                @buscar="buscar"
            />

            <!-- TABLA -->
            <MiDepartamentoTable
                :llamadas="llamadas"
            />

        </div>

    </div>
</template>

<script>
import MiDepartamentoHeader from './components/MiDepartamentoHeader.vue'
import MiDepartamentoResumen from './components/MiDepartamentoResumen.vue'
import MiDepartamentoFiltros from './components/MiDepartamentoFiltros.vue'
import MiDepartamentoTable from './components/MiDepartamentoTable.vue'

import './styles/mi-departamento.css'

export default {

    name: 'DepartamentoIndex',

    components: {
        MiDepartamentoHeader,
        MiDepartamentoResumen,
        MiDepartamentoFiltros,
        MiDepartamentoTable
    },

    data() {
        return {

            resumen: {
                total: 24,
                proceso: 8,
                transferidas: 5,
                finalizadas: 11
            },

            llamadas: [

                {
                    id: 1,
                    folio: 'CALL-20260824-0001',
                    fecha: '24/08/2026',
                    hora: '08:15',
                    nombre: 'Juan Pérez',
                    telefono: '6121234567',
                    categoria: 'Soporte',
                    estado: 'en_proceso',
                    estadoTexto: 'En proceso'
                },

                {
                    id: 2,
                    folio: 'CALL-20260824-0002',
                    fecha: '24/08/2026',
                    hora: '08:42',
                    nombre: 'María González',
                    telefono: '6127654321',
                    categoria: 'Información',
                    estado: 'transferida',
                    estadoTexto: 'Transferida'
                },

                {
                    id: 3,
                    folio: 'CALL-20260824-0003',
                    fecha: '24/08/2026',
                    hora: '09:03',
                    nombre: 'Carlos Ramírez',
                    telefono: '6129876543',
                    categoria: 'Soporte',
                    estado: 'finalizada',
                    estadoTexto: 'Finalizada'
                }

            ]

        }
    },

    methods: {

        actualizar() {
            // Aquí posteriormente conectaremos la API.
            console.log('Actualizando llamadas...')
        },

        buscar(filtros) {
            // Aquí posteriormente conectaremos los filtros con la API.
            console.log('Filtros:', filtros)
        }

    }

}
</script>
```
