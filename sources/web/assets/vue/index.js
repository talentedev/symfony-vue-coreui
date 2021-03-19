// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import "core-js/stable";
import "regenerator-runtime/runtime";
import 'intersection-observer';

import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue'
import App from './App';
import router from './router';
import store from './store';
import i18n from './locales';

Vue.use(BootstrapVue);

new Vue({
    template: '<App/>',
    components: { App },
    router,
    store,
    i18n
}).$mount('#app');