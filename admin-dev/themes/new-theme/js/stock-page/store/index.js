import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import products from './modules/products';

Vue.use(Vuex);

// root state object.

const state = {
  order: 'product',
  pageIndex: 0,
  totalPages: 0,
  productsPerPage: 100,
  combinationsPerPage: 50,
  keywords: []
};

// getters are functions
const getters = {
  hasQty(state) {
    return state.products.hasQty;
  },
  totalPages(state) {
    return state.totalPages;
  },
  pageIndex(state) {
    return state.pageIndex;
  },
  products(state) {
    return state.products.products;
  }
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations,
  modules: {
    products
  }
});