require('../app');

window.Vue = require('vue');

import Flash from '../components/Flash.vue' ;

var app = new Vue({

    name: 'App',
    
    el: '#app',

    components: { Flash }
});