
require('./bootstrap');

window.Vue = require('vue');

const app = new Vue({
    el: '#app',

    methods: {
        submit(form) {
            jQuery('.errors').hide();
            jQuery(form).submit();
        }
    }
});
