import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from './utils/growl';

Vue.use(VueResource);

export const getStock = ({ commit, state }, payload) => {
  let url = window.data.apiRootUrl.replace(/\?.*/, '');
  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : []
    }
  }).then(function(response) {
    commit(types.SET_PAGE_INDEX, payload.page_index);
    commit(types.UPDATE_ORDER, payload.order);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.ADD_PRODUCTS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getSuppliers = ({ commit }) => {
  let url = `${window.data.baseUrl}/api/suppliers`;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_SUPPLIERS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getCategories = ({ commit }) => {
  let url = `${window.data.baseUrl}/api/categories`;
  let categories = [];
  Vue.http.get(url).then(function(response) {
    for(let category in response.body) {
      categories.push(response.body[category]);
    }
    commit(types.SET_CATEGORIES, categories);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};
