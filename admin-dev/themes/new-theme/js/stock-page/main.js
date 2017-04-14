import Vue from 'vue';
import app from './components/app';
import store from './store/';

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
