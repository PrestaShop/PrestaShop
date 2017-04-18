import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import products from './modules/products';
import _ from 'lodash';

Vue.use(Vuex);

// root state object.

const state = {
  order: 'product',
  pageIndex: 0,
  totalPages: 0,
  productsPerPage: 100,
  combinationsPerPage: 50,
  suppliers: [],
  categories: []
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
  },
  categories(state) {
    function convert(categories) {
      categories.forEach((category)=>{
        category.children = _.values(category.children);
        category.id = category.id_category;
        convert(category.children);
      });
      return categories;
    }

    return convert(state.categories);
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
