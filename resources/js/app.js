require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import App from './Components/App';

const app = new Vue({
    el: '#app',
    components: { App},
  });