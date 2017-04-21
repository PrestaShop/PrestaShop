import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import products from './modules/products';
import _ from 'lodash';

Vue.use(Vuex);

// root state object.

const state = {
  order: '',
  pageIndex: 1,
  totalPages: 0,
  productsPerPage: 100,
  combinationsPerPage: 50,
  suppliers: [],
  categories: [],
  categoryList: [],
  movements: [],
  translations: {}
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
  order(state) {
    return state.order;
  },
  products(state) {
    return state.products.products;
  },
  suppliers(state) {
    return state.suppliers;
  },
  categories(state) {
    function convert(categories) {
      categories.forEach((category)=>{
        category.children = _.values(category.children);
        state.categoryList.push(category);
        convert(category.children);
      });
      return categories;
    }

    return convert(state.categories);
  },
  categoryList(state) {
    return state.categoryList;
  },
  movements(state) {
    return state.movements;
  },
  translations(state) {
    return state.translations;
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
