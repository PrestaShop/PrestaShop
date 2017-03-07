import Vue from 'vue';
import app from './components/app';
import store from './store'

const stockApp = new Vue({
  store,
  el: '#stock-app',
  template: '<app/>',
  components: { app }
});
