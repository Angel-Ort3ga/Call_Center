import Vue from 'vue';
import VueRouter from 'vue-router';

import AdminLayout from '../admin/layouts/AdminLayout.vue';
import Dashboard from '../admin/modules/Dashboard/index.vue';
import Login from '../auth/Login.vue';
import Llamadas from '../admin/modules/Llamadas/index.vue';
import Departamentos from '../admin/modules/Departamentos/index.vue';
import MiDepartamento from '../admin/modules/MiDepartamento/index.vue';
import Usuarios from '../admin/modules/Usuarios/index.vue';

import {
    isAuthenticated,
} from '../auth/auth';

Vue.use(VueRouter);

const routes = [

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    {
        path: '/login',

        name: 'login',

        component: Login,

        meta: {
            guest: true,
        },
    },


    /*
    |--------------------------------------------------------------------------
    | Administración
    |--------------------------------------------------------------------------
    */

    {
        path: '/',

        component: AdminLayout,

        meta: {
            requiresAuth: true,
        },

        children: [

            {
                path: '',

                redirect: '/dashboard',
            },


            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            {
                path: 'dashboard',

                name: 'dashboard',

                component: Dashboard,

                meta: {
                    requiresAuth: true,
                },
            },


            /*
            |--------------------------------------------------------------------------
            | Llamadas
            |--------------------------------------------------------------------------
            */

            {
                path: 'llamadas',

                name: 'llamadas',

                component: Llamadas,

                meta: {
                    requiresAuth: true,
                },
            },


            /*
            |--------------------------------------------------------------------------
            | Departamentos
            |--------------------------------------------------------------------------
            */

            {
                path: 'departamentos',

                name: 'departamentos',

                component: Departamentos,

                meta: {
                    requiresAuth: true,
                },
            },


            /*
            |--------------------------------------------------------------------------
            | Mi departamento
            |--------------------------------------------------------------------------
            */

            {
                path: 'mi-departamento',

                name: 'mi-departamento',

                component: MiDepartamento,

                meta: {
                    requiresAuth: true,
                },
            },


            /*
            |--------------------------------------------------------------------------
            | Usuarios
            |--------------------------------------------------------------------------
            */

            {
                path: 'usuarios',

                name: 'usuarios',

                component: Usuarios,

                meta: {
                    requiresAuth: true,
                },
            },

        ],
    },

];


const router = new VueRouter({

    mode: 'history',

    routes,

});


/*
|--------------------------------------------------------------------------
| Protección de rutas
|--------------------------------------------------------------------------
*/

router.beforeEach((to, from, next) => {

    const autenticado = isAuthenticated();


    /*
    |--------------------------------------------------------------------------
    | Ruta protegida
    |--------------------------------------------------------------------------
    */

    if (
        to.matched.some(
            route => route.meta.requiresAuth
        )
        &&
        !autenticado
    ) {

        next({
            name: 'login',
        });

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Usuario autenticado intentando entrar al Login
    |--------------------------------------------------------------------------
    */

    if (
        to.matched.some(
            route => route.meta.guest
        )
        &&
        autenticado
    ) {

        next({
            name: 'dashboard',
        });

        return;
    }


    next();

});


export default router;