import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from './utils/growl';

Vue.use(VueResource);

export const getStock = ({ commit, state }, payload) => {
  let url = window.data.apiRootUrl.replace(/\?.*/, '');
  if (payload.keywords) {
    state.keywords = payload.keywords;
  }
  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: state.keywords
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
