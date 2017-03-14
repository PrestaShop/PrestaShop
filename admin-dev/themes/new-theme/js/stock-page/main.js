import Vue from 'vue';
import VueResource from 'vue-resource';
import app from './components/app';
import store from './store/';

Vue.use(VueResource);
window._ = require('lodash');

const stockApp = new Vue({
  store,
  el: '#stock-app',
  template: '<app/>',
  components: { app },
  methods:{
     getStock: function(){
       this.$http.get(data.apiUrl).then(function(response){
         if(response.status == 200) {
           this.$store.commit('addProducts', response.body);
         }
       }, function(error){
           console.log(error.statusText);
       });
     }
   },
   mounted: function () {
     this.getStock();
   }
});
