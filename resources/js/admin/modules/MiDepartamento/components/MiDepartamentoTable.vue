```vue
<template>
    <div class="table-container">

        <table>

            <thead>

                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th></th>
                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="llamada in llamadas"
                    :key="llamada.id"
                >

                    <td>
                        <span class="folio">
                            {{ llamada.folio }}
                        </span>
                    </td>

                    <td>
                        {{ llamada.fecha }}
                    </td>

                    <td>
                        {{ llamada.hora }}
                    </td>

                    <td>
                        {{ llamada.nombre }}
                    </td>

                    <td>
                        {{ llamada.telefono }}
                    </td>

                    <td>
                        {{ llamada.categoria }}
                    </td>

                    <td>

                        <span
                            class="status"
                            :class="llamada.estado"
                        >
                            {{ llamada.estadoTexto }}
                        </span>

                    </td>

                    <td>

                        <button
                            type="button"
                            class="view-button"
                        >
                            <i class="fas fa-eye"></i>

                            Ver
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>
</template>

<script>
export default {

    name: 'MiDepartamentoTable',

    props: {

        llamadas: {
            type: Array,
            required: true
        }

    }

}
</script>
```
