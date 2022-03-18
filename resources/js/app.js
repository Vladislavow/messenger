/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import App from './Components/App.vue';
import VueRouter from 'vue-router';
import routes from './routes';
import vuetify from './vuetify';
import 'vuetify/dist/vuetify.min.css'
import Axios from 'axios'
import store from './store';
import InfiniteLoading from 'vue-infinite-loading';

const router = new VueRouter(routes);
router.beforeEach((to, from, next) => {
    if (store.getters.isLoggedIn && (to.name == 'login' || to.name == 'register')) {
      next('/')
    }
    if (to.matched.some(record => record.meta.requiresAuth)) {
      if (store.getters.isLoggedIn) {
        next()
        return
      }
      next('/login')
    } else {
      next()
    }
  })
Vue.use(VueRouter);
Vue.prototype.$http = Axios;
const token = localStorage.getItem('token')
if (token) {
  Vue.prototype.$http.defaults.headers.common['Authorization'] = `Bearer ${token}`
}
Vue.use(InfiniteLoading);
// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

const app = new Vue({
    el: '#app',
    components: {App},
    router,    
    store,
    vuetify
});
