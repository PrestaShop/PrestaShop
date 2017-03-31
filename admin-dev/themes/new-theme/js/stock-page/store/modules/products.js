import * as types from '../mutation-types';
import Vue from 'vue';
import VueResource from 'vue-resource';
import { showGrowl } from '../utils/growl';

Vue.use(VueResource);

// initial state
const state = {
  productsToUpdate: [],
  products: [],
  hasQty: false
};

// getters
const getters = {

};

// actions
const actions = {
  updateQtyByProductId({ commit, state }, payload) {
    let url = payload.url,
      delta = payload.delta;

    Vue.http.post(url, {
      delta
    }).then((res) => {
      commit(types.UPDATE_PRODUCT, res.body);
      return showGrowl('notice', 'Stock successfully updated');
    }, function(error) {
      return showGrowl('error', error.statusText);
    });
  },

  updateQtyByProductsId({ commit, state }, payload) {
    let url = payload.url,
      productsQty = state.productsToUpdate;

    Vue.http.post(url, productsQty).then((res) => {
      commit(types.UPDATE_PRODUCTS, res.body);
      return showGrowl('notice', 'Stock successfully updated');
    }, function(error) {
      return showGrowl('error', error.statusText);
    });
  }
};

// mutations
const mutations = {
  [types.ADD_PRODUCTS](state, products) {

    window._.forEach(products, (product) => {
      product.qty = 0;
    });

    state.products = products;
  },

  [types.UPDATE_PRODUCT](state, updatedProduct) {
    let index = window._.findIndex(state.products, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });
    updatedProduct.qty = 0;
    state.products.splice(index, 1, updatedProduct);
  },

  [types.UPDATE_PRODUCTS](state, updatedProducts) {
    state.productsToUpdate = [];
    window._.forEach(updatedProducts, (product) => {
      let index = window._.findIndex(state.products, {
        'product_id': product.product_id,
        'combination_id': product.combination_id
      });
      product.qty = 0;
      state.products.splice(index, 1, product);
    });
    state.hasQty = false;
  },

  [types.UPDATE_PRODUCT_QTY](state, updatedProduct) {
    let hasQty = false;

    let index = window._.findIndex(state.productsToUpdate, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });

    let productToUpdate = window._.find(state.products, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });

    window._.forEach(state.products, (product) => {
      productToUpdate.qty = updatedProduct.delta;
      if (product.qty) {
        hasQty = true;
      }
    });

    state.hasQty = hasQty;

    if (index !== -1) {
      return state.productsToUpdate.splice(index, 1, updatedProduct);
    }

    state.productsToUpdate.push(updatedProduct);
  }
};

export default {
  state,
  getters,
  actions,
  mutations
};