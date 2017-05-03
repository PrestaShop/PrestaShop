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
  keywords:[],
  suppliers: [],
  categories: [],
  categoryList: [],
  movements: [],
  employees: [],
  movementsTypes: [],
  translations: {},
  isLoading: false,
  isReady: false
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
  keywords(state) {
    return state.keywords;
  },
  suppliers(state) {
    function convert(suppliers) {
      suppliers.forEach((supplier)=>{
        supplier.id = supplier.supplier_id;
      });
      return suppliers;
    }

    return convert(state.suppliers);
  },
  categories(state) {
    function convert(categories) {
      categories.forEach((category)=>{
        category.children = _.values(category.children);
        state.categoryList.push(category);
        category.id = `${category.id_parent}-${category.id_category}`;
        convert(category.children);
      });
      return categories;
    }

    return convert(state.categories);
  },
  employees(state) {
    return state.employees;
  },
  movementsTypes(state) {
    return state.movementsTypes;
  },
  categoryList(state) {
    return state.categoryList;
  },
  movements(state) {
    return state.movements;
  },
  translations(state) {
    return state.translations;
  },
  isLoading(state) {
    return state.isLoading;
  },
  isReady(state) {
    return state.isReady;
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
