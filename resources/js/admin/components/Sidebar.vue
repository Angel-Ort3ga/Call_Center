<template>
    <aside
        class="w-64 min-h-screen bg-gray-900 text-white flex flex-col"
    >
        <!-- Logo -->
        <div class="px-6 py-5 border-b border-gray-700">
            <h1 class="text-xl font-bold">
                Call Center
            </h1>

            <p class="text-sm text-gray-400 mt-1">
                Sistema de gestión
            </p>
        </div>

        <!-- Usuario -->
        <div class="px-6 py-4 border-b border-gray-700">
            <p class="text-sm font-medium">
                {{ nombreUsuario }}
            </p>

            <p class="text-xs text-gray-400 mt-1">
                {{ nombreRol }}
            </p>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-4 py-6 space-y-2">

            <!-- Dashboard -->
            <router-link
                v-if="puedeVerDashboard"
                to="/dashboard"
                class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition"
                active-class="bg-gray-800 text-white"
            >
                Dashboard
            </router-link>

            <!-- Llamadas -->
            <router-link
                v-if="puedeVerLlamadas"
                to="/llamadas"
                class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition"
                active-class="bg-gray-800 text-white"
            >
                Llamadas
            </router-link>

            <!-- Departamentos -->
            <router-link
                v-if="puedeVerDepartamentos"
                to="/departamentos"
                class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition"
                active-class="bg-gray-800 text-white"
            >
                Departamentos
            </router-link>

            <!-- Mi Departamento -->
            <router-link
                v-if="puedeVerMiDepartamento"
                to="/mi-departamento"
                class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition"
                active-class="bg-gray-800 text-white"
            >
                Mi Departamento
            </router-link>

            <!-- Usuarios -->
            <router-link
                v-if="puedeVerUsuarios"
                to="/usuarios"
                class="flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition"
                active-class="bg-gray-800 text-white"
            >
                Usuarios
            </router-link>

        </nav>

        <!-- Cerrar sesión -->
        <div class="p-4 border-t border-gray-700">

            <button
                type="button"
                class="w-full px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 transition text-sm"
                @click="cerrarSesion"
            >
                Cerrar sesión
            </button>

        </div>
    </aside>
</template>

<script>
import {
    getUser,
    cerrarSesion as cerrarSesionAuth,
} from '../../auth/auth';

export default {
    name: 'Sidebar',

    data() {
        return {
            usuario: getUser(),
        };
    },

    computed: {

        /*
        |--------------------------------------------------------------------------
        | Nombre del usuario
        |--------------------------------------------------------------------------
        */

        nombreUsuario() {
            if (!this.usuario) {
                return 'Usuario';
            }

            return `${this.usuario.nombre} ${this.usuario.apellido}`;
        },

        /*
        |--------------------------------------------------------------------------
        | Nombre del rol
        |--------------------------------------------------------------------------
        */

        nombreRol() {
            const roles = {
                super_admin: 'Super Administrador',
                supervisor: 'Supervisor',
                jefe_departamento: 'Jefe de departamento',
                recepcionista: 'Recepcionista',
            };

            return roles[this.usuario?.role] || 'Usuario';
        },

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        puedeVerDashboard() {
            return [
                'super_admin',
                'supervisor',
            ].includes(this.usuario?.role);
        },

        /*
        |--------------------------------------------------------------------------
        | Llamadas
        |--------------------------------------------------------------------------
        */

        puedeVerLlamadas() {
            return [
                'super_admin',
                'supervisor',
                'jefe_departamento',
                'recepcionista',
            ].includes(this.usuario?.role);
        },

        /*
        |--------------------------------------------------------------------------
        | Departamentos
        |--------------------------------------------------------------------------
        */

        puedeVerDepartamentos() {
            return [
                'super_admin',
                'supervisor',
            ].includes(this.usuario?.role);
        },

        puedeVerMiDepartamento() {
            return [
                'super_admin',
                'supervisor',
                'jefe_departamento',
            ].includes(this.usuario?.role);
        },

        /*
        |--------------------------------------------------------------------------
        | Usuarios
        |--------------------------------------------------------------------------
        */

        puedeVerUsuarios() {
            return this.usuario?.role === 'super_admin';
        },
    },

    methods: {

        async cerrarSesion() {
            await cerrarSesionAuth();

            this.$router.push('/login');
        },
    },
};
</script>