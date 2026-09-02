<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

        <div class="w-full max-w-md">

            <!-- Encabezado -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    Call Center
                </h1>

                <p class="mt-2 text-gray-500">
                    Sistema de gestión de llamadas
                </p>
            </div>

            <!-- Tarjeta -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">

                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    Iniciar sesión
                </h2>

                <!-- Error -->
                <div
                    v-if="error"
                    class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 text-sm"
                >
                    {{ error }}
                </div>

                <!-- Email -->
                <div class="mb-5">
                    <label
                        for="email"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Correo electrónico
                    </label>

                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        placeholder="correo@ejemplo.com"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="cargando"
                        @keyup.enter="iniciarSesion"
                    >
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label
                        for="password"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        Contraseña
                    </label>

                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :disabled="cargando"
                        @keyup.enter="iniciarSesion"
                    >
                </div>

                <!-- Botón -->
                <button
                    type="button"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-medium rounded-lg px-4 py-3 transition"
                    :disabled="cargando"
                    @click="iniciarSesion"
                >
                    <span v-if="!cargando">
                        Iniciar sesión
                    </span>

                    <span v-else>
                        Iniciando sesión...
                    </span>
                </button>

            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Sistema de gestión de Call Center
            </p>

        </div>
    </div>
</template>

<script>
import axios from 'axios';

import {
    iniciarSesion,
} from './auth';

export default {
    name: 'Login',

    data() {
        return {
            form: {
                email: '',
                password: '',
            },

            cargando: false,

            error: null,
        };
    },

    methods: {
        async iniciarSesion() {
            this.error = null;

            if (!this.form.email || !this.form.password) {
                this.error =
                    'Ingresa tu correo electrónico y contraseña.';

                return;
            }

            this.cargando = true;

            try {
                const response = await axios.post(
                    '/api/admin/login',
                    {
                        email: this.form.email,
                        password: this.form.password,
                    }
                );

                const data = response.data;

                if (!data.success || !data.token) {
                    this.error =
                        data.message ||
                        'No fue posible iniciar sesión.';

                    return;
                }

                iniciarSesion(
                    data.token,
                    data.user
                );

                /*
                |--------------------------------------------------------------------------
                | Ir al Dashboard
                |--------------------------------------------------------------------------
                */

                this.$router.push('/dashboard');

            } catch (error) {

                console.error(
                    'Error al iniciar sesión:',
                    error
                );

                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.error =
                        error.response.data.message;
                } else {
                    this.error =
                        'No fue posible conectar con el servidor.';
                }

            } finally {
                this.cargando = false;
            }
        },
    },
};
</script>