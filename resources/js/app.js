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
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

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
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Axios.interceptors.response.use((response) => {
  return response;
}, function (error) {
  if (error.response.status === 401) {
    localStorage.removeItem('token')
    localStorage.removeItem('userid')
    router.push('/')
  }
  return Promise.reject(error);
});
Vue.use(Toast,{
  position: POSITION.BOTTOM_RIGHT
});
const app = new Vue({
    el: '#app',
    components: {App},
    router,    
    store,
    vuetify
});
