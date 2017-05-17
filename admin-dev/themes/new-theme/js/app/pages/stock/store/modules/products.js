/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import * as types from '../mutation-types';
import Vue from 'vue';
import VueResource from 'vue-resource';
import { showGrowl } from 'app/utils/growl';
import _ from 'lodash';

Vue.use(VueResource);

// initial state
const state = {
  productsToUpdate: [],
  products: [],
  hasQty: false,
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
    _.forEach(products.data, (product) => {
      product.qty = 0;
    });

    state.products = products;
  },

  [types.UPDATE_PRODUCT](state, updatedProduct) {
    const index = _.findIndex(state.products.data, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });
    updatedProduct.qty = 0;
    state.products.data.splice(index, 1, updatedProduct);
  },

  [types.UPDATE_PRODUCTS](state, updatedProducts) {
    state.productsToUpdate = [];
    _.forEach(updatedProducts, (product) => {
      const index = _.findIndex(state.products.data, {
        product_id: product.product_id,
        combination_id: product.combination_id,
      });
      product.qty = 0;
      state.products.data.splice(index, 1, product);
    });
    state.hasQty = false;
  },

  [types.UPDATE_PRODUCT_QTY](state, updatedProduct) {
    let hasQty = false;

    const index = _.findIndex(state.productsToUpdate, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });

    const productToUpdate = _.find(state.products.data, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });

    _.forEach(state.products.data, (product) => {
      productToUpdate.qty = updatedProduct.delta;
      if (product.qty) {
        hasQty = true;
      }
    });

    state.hasQty = hasQty;

    if (index !== -1) {
      return state.productsToUpdate.splice(index, 1, updatedProduct);
    }
    if (updatedProduct.delta) {
      state.productsToUpdate.push(updatedProduct);
    }
  },
};

export default {
  state,
  getters,
  actions,
  mutations,
};
