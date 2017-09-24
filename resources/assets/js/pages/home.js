require('../app');

window.Vue = require('vue');

import Balance from '../components/Balance.vue' 

var app = new Vue({
    name: 'App',
    el: '#app',
    components: { Balance },
    data: {
        test: 'This is from the welcome page component'
    }
})