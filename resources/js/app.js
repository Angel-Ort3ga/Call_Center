import './bootstrap';

import Vue from 'vue';

import App from './App.vue';

import router from './router';

import {
    restaurarSesion,
} from './auth/auth';

Vue.config.productionTip = false;

/*
|--------------------------------------------------------------------------
| Restaurar sesión
|--------------------------------------------------------------------------
*/

restaurarSesion();

/*
|--------------------------------------------------------------------------
| Iniciar Vue
|--------------------------------------------------------------------------
*/

new Vue({
    router,

    render: h => h(App),
}).$mount('#app');