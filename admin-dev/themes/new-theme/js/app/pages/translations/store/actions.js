import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from 'app/utils/growl';

Vue.use(VueResource);

export const getTranslations = ({ commit }) => {
  let url = window.data.translationUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_TRANSLATIONS, response.body);
    commit(types.APP_IS_READY);
  }, function(error) {
    return showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const getCatalog = ({ commit }, payload) => {
  Vue.http.get(payload.url, {
    params: {
      page_size: payload.page_size,
      page_index: payload.page_index,
    }
  }).then(function(response) {
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_CATALOG, response.body);
  }, function(error) {
    return showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const getDomainsTree = ({ commit }) => {
  let url = window.data.domainsTreeUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_DOMAINS_TREE, response.body);
  }, function(error) {
    return showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const saveTranslations =  ({ commit }, payload) => {
  let url = payload.url,
    translations = payload.translations;

  Vue.http.post(url, {
    translations
  }).then((res) => {
    return showGrowl('notice', 'Translations successfully updated');
  }, function(error) {
    return showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const resetTranslation =  ({ commit }, payload) => {
  let url = payload.url,
    translations = payload.translations;

  Vue.http.post(url, {
    translations
  }).then((res) => {
    return showGrowl('notice', 'Translations successfully reset');
  }, function(error) {
    return showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const updatePageIndex = ({ commit }, pageIndex) => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};
