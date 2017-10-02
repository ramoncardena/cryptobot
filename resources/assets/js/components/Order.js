require('../app');

window.Vue = require('vue');

import Order from '../components/Order.vue' ;

var app = new Vue({

    name: 'App',
    
    el: '#app',

    components: { Order }
});