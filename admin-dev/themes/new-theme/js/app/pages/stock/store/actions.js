import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from 'app/utils/growl';
import _ from 'lodash';

Vue.use(VueResource);

export const getStock = ({ commit }, payload) => {
  let url = window.data.apiRootUrl.replace(/\?.*/, '');
  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : [],
      category_id: payload.categories ? payload.categories : []
    }
  }).then(function(response) {
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

export const getMovements = ({ commit }, payload) => {
  let url = `${window.data.baseUrl}/api/movements`;

  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : [],
      category_id: payload.categories ? payload.categories : []
    }
  }).then(function(response) {
    commit(types.SET_MOVEMENTS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getTranslations = ({ commit }) => {
  let url = `${window.data.baseUrl}/api/i18n/stock`;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_TRANSLATIONS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const updateOrder = ({ commit }, order) => {
  commit(types.UPDATE_ORDER, order);
};

export const updatePageIndex = ({ commit }, pageIndex) => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};

export const updateKeywords = ({ commit }, keywords) => {
  commit(types.UPDATE_KEYWORDS, keywords);
};