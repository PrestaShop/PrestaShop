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
    payload.store.dispatch('getDomainsTree');
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

export const updateCurrentDomain = ({ commit }, currentDomain) => {
  commit(types.SET_CURRENT_DOMAIN, currentDomain);
};
