import Vue from 'vue'
import moment from 'moment'
Vue.filter('timeFormat',(arg)=>{
    return moment(arg).startOf('hour').fromNow();
});

Vue.filter('sortlenght',function(text,length,suffix){
    return text.substring(0,length)+suffix
});
