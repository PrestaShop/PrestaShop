import Vue from 'vue';
import app from './components/app';
import store from './store/';

window._ = require('lodash');
const DEFAULT_LINE_NUMBER = 10;
const stockApp = new Vue({
  store,
  el: '#stock-app',
  template: '<app/>',
  components: { app },
  mounted() {
    this.$store.dispatch('getStock', {
      url: window.data.apiRootUrl.replace(/\?.*/,''),
      order: this.$store.state.order,
      page_size: DEFAULT_LINE_NUMBER,
      page_index: 1
    });
  }
});
