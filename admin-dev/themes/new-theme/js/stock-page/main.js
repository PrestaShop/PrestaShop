import Vue from 'vue';
import app from './components/app';
import store from './store/';

window._ = require('lodash');
const DEFAULT_PRODUCTS_NUMBER = 10;
const stockApp = new Vue({
  store,
  el: '#stock-app',
  template: '<app />',
  components: { app },
  mounted() {
    this.$store.dispatch('getStock', {
      order: this.$store.state.order,
      page_size: this.$store.state.productsPerPage,
      page_index: 1
    });
  }
});