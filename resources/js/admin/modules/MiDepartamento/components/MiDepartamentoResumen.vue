```vue
<template>
    <div class="stats-grid">

        <!-- TOTAL -->
        <div class="stat-card">

            <div class="stat-icon total">
                <i class="fas fa-phone"></i>
            </div>

            <div>
                <span class="stat-label">
                    Total de llamadas
                </span>

                <strong class="stat-value">
                    {{ resumen.total }}
                </strong>
            </div>

        </div>

        <!-- EN PROCESO -->
        <div class="stat-card">

            <div class="stat-icon process">
                <i class="fas fa-clock"></i>
            </div>

            <div>
                <span class="stat-label">
                    En proceso
                </span>

                <strong class="stat-value">
                    {{ resumen.proceso }}
                </strong>
            </div>

        </div>

        <!-- TRANSFERIDAS -->
        <div class="stat-card">

            <div class="stat-icon transferred">
                <i class="fas fa-exchange-alt"></i>
            </div>

            <div>
                <span class="stat-label">
                    Transferidas
                </span>

                <strong class="stat-value">
                    {{ resumen.transferidas }}
                </strong>
            </div>

        </div>

        <!-- FINALIZADAS -->
        <div class="stat-card">

            <div class="stat-icon finished">
                <i class="fas fa-check"></i>
            </div>

            <div>
                <span class="stat-label">
                    Finalizadas
                </span>

                <strong class="stat-value">
                    {{ resumen.finalizadas }}
                </strong>
            </div>

        </div>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoResumen',

    props: {
        resumen: {
            type: Object,
            required: true
        }
    }

}
</script>
```
