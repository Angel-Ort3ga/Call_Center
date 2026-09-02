```vue
<template>
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Mi Departamento
            </h1>

            <p class="page-subtitle">
                Consulta y seguimiento de las llamadas recibidas.
            </p>
        </div>

        <div class="department-name">
            <i class="fas fa-building"></i>

            <span>
                Soporte Técnico
            </span>
        </div>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoHeader',

    methods: {

        actualizar() {
            this.$emit('actualizar')
        }

    }

}
</script>
```
