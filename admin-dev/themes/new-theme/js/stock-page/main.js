import Vue from 'vue';
import app from './components/app';
import store from './store/';

window._ = require('lodash');

const stockApp = new Vue({
  store,
  el: '#stock-app',
  template: '<app/>',
  components: { app },
  mounted() {
    this.$store.dispatch('getStock', {
      url: window.data.apiRootUrl
    });
  }
});
