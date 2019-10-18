
require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)
import {routes} from './routes'



const router = new VueRouter({
    routes,
    mode:'history'
})

//v-chat scroll
import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)


//vuex
import Vuex from 'vuex'
Vue.use(Vuex)
import storeData from './store/index'
const store = new Vuex.Store(
    storeData
);


//Date Time Format using Moment js
import {filter} from './Helper/filter'


//sweet alert2
import Swal from 'sweetalert2';
window.Swal = Swal;
const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
window.toast = toast;


const app = new Vue({
    el: '#app',
    router,
    store
});
