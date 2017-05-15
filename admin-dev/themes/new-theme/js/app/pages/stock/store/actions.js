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
    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.ADD_PRODUCTS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getSuppliers = ({ commit }) => {
  let url = window.data.suppliersUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_SUPPLIERS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getCategories = ({ commit }) => {
  let url = window.data.categoriesUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_CATEGORIES, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getMovements = ({ commit }, payload) => {
  let url = window.data.movementsUrl;

  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : [],
      category_id: payload.categories ? payload.categories : [],
      id_stock_mvt_reason: payload.id_stock_mvt_reason ? payload.id_stock_mvt_reason : [],
      id_employee: payload.id_employee ? payload.id_employee : [],
      date_add: payload.date_add ? payload.date_add : []
    }
  }).then(function(response) {
    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_MOVEMENTS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getTranslations = ({ commit }) => {
  let url = window.data.translationUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_TRANSLATIONS, response.body);
    commit(types.APP_IS_READY);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getEmployees = ({ commit }) => {
  let url = window.data.employeesUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_EMPLOYEES_LIST, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};

export const getMovementsTypes = ({ commit }) => {
  let url = window.data.movementsTypesUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_MOVEMENTS_TYPES, response.body);
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

export const isLoading = ({ commit }) => {
  commit(types.LOADING_STATE, true);
};