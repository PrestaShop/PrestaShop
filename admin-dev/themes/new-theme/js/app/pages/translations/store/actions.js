/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from 'app/utils/growl';

Vue.use(VueResource);

export const getTranslations = ({ commit }) => {
  const url = window.data.translationUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_TRANSLATIONS, response.body);
    commit(types.APP_IS_READY);
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const getCatalog = ({ commit }, payload) => {
  commit(types.PRINCIPAL_LOADING, true);
  Vue.http.get(payload.url, {
    params: {
      page_size: payload.page_size,
      page_index: payload.page_index,
    },
  }).then((response) => {
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_CATALOG, response.body);
    commit(types.PRINCIPAL_LOADING, false);
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const getDomainsTree = ({ commit }, payload) => {
  const url = window.data.domainsTreeUrl;
  const params = {};

  commit(types.SIDEBAR_LOADING, true);
  commit(types.PRINCIPAL_LOADING, true);

  if (payload.store.getters.searchTags.length) {
    params.search = payload.store.getters.searchTags;
  }

  Vue.http.get(url, {
    params,
  }).then((response) => {
    commit(types.SET_DOMAINS_TREE, response.body);
    commit(types.SIDEBAR_LOADING, false);
    commit(types.RESET_CURRENT_DOMAIN);
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const refreshCounts = ({ commit }, payload) => {
  const url = window.data.domainsTreeUrl;
  const params = {};

  if (payload.store.getters.searchTags.length) {
    params.search = payload.store.getters.searchTags;
  }

  Vue.http.get(url, {
    params,
  }).then((response) => {
    payload.store.state.currentDomainTotalMissingTranslations -= payload.successfullySaved;
    commit(types.SET_DOMAINS_TREE, response.body);
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const saveTranslations = ({ commit }, payload) => {
  const url = payload.url;
  const translations = payload.translations;

  Vue.http.post(url, {
    translations,
  }).then(() => {
    payload.store.dispatch('refreshCounts', {
      successfullySaved: translations.length,
      store: payload.store,
    });
    payload.store.state.modifiedTranslations = [];
    return showGrowl('notice', 'Translations successfully updated');
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const resetTranslation = ({ commit }, payload) => {
  const url = payload.url;
  const translations = payload.translations;

  Vue.http.post(url, {
    translations,
  }).then(() => {
    showGrowl('notice', 'Translations successfully reset');
  }, (error) => {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  });
};

export const updatePageIndex = ({ commit }, pageIndex) => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};

export const updateCurrentDomain = ({ commit }, currentDomain) => {
  commit(types.SET_CURRENT_DOMAIN, currentDomain);
};

export const updatePrincipalLoading = ({ commit }, principalLoading) => {
  commit(types.PRINCIPAL_LOADING, principalLoading);
};

export const updateSearch = ({ commit }, searchTags) => {
  commit(types.SEARCH_TAGS, searchTags);
};
