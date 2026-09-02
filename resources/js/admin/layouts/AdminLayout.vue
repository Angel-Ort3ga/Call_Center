<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Contenido principal -->
        <main class="flex-1 min-w-0">
            <div class="p-6">
                <router-view />
            </div>
        </main>
    </div>
</template>

<script>
import Sidebar from '../components/Sidebar.vue';

export default {
    name: 'AdminLayout',

    components: {
        Sidebar,
    },
};
</script>