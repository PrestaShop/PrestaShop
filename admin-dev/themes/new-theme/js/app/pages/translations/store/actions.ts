/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import Vue from 'vue';
import {Commit} from 'vuex/types';
import VueResource from 'vue-resource';
import * as types from '@app/pages/translations/store/mutation-types';
import {showGrowl} from '@app/utils/growl';

Vue.use(VueResource);

export const getTranslations = ({commit}: {commit: Commit}): void => {
  const url = window.data.translationUrl;
  Vue.http.get(url).then(
    (response: Record<string, any>) => {
      commit(types.SET_TRANSLATIONS, response.body);
      commit(types.APP_IS_READY);
    },
    (error) => {
      showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
    },
  );
};

export const getCatalog = ({commit}: {commit: Commit}, payload: Record<string, any>): void => {
  commit(types.PRINCIPAL_LOADING, true);
  Vue.http
    .get(payload.url, {
      params: {
        page_size: payload.page_size,
        page_index: payload.page_index,
      },
    })
    .then(
      (response: Record<string, any>) => {
        commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
        commit(types.SET_CATALOG, response.body);
        commit(types.PRINCIPAL_LOADING, false);
      },
      (error) => {
        showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
      },
    );
};

export const getDomainsTree = ({commit}: {commit: Commit}, payload: Record<string, any>): void => {
  const url = window.data.domainsTreeUrl;
  const params: {search: Array<string>} = {search: []};

  commit(types.SIDEBAR_LOADING, true);
  commit(types.PRINCIPAL_LOADING, true);

  if (payload.store.getters.searchTags.length) {
    params.search = payload.store.getters.searchTags;
  }

  Vue.http
    .get(url, {
      params,
    })
    .then(
      (response: Record<string, any>) => {
        commit(types.SET_DOMAINS_TREE, response.body);
        commit(types.SIDEBAR_LOADING, false);
        commit(types.RESET_CURRENT_DOMAIN);
      },
      (error) => {
        showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
      },
    );
};

export const refreshCounts = ({commit}: {commit: Commit}, payload: Record<string, any>): void => {
  const url = window.data.domainsTreeUrl;
  const params: {search: Array<string>} = {search: []};

  if (payload.store.getters.searchTags.length) {
    params.search = payload.store.getters.searchTags;
  }

  Vue.http
    .get(url, {
      params,
    })
    .then(
      (response: Record<string, any>) => {
        commit(types.DECREASE_CURRENT_DOMAIN_TOTAL_MISSING_TRANSLATIONS, payload.successfullySaved);
        commit(types.SET_DOMAINS_TREE, response.body);
      },
      (error) => {
        showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
      },
    );
};

export const saveTranslations = ({commit}: {commit: Commit}, payload: Record<string, any>): void => {
  const {url} = payload;
  const {translations} = payload;

  Vue.http
    .post(url, {
      translations,
    })
    .then(
      () => {
        payload.store.dispatch('refreshCounts', {
          successfullySaved: translations.length,
          store: payload.store,
        });
        commit(types.RESET_MODIFIED_TRANSLATIONS);
        return showGrowl('success', 'Translations successfully updated');
      },
      (error) => {
        showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
      },
    );
};

/* eslint-disable-next-line no-unused-vars */
export const resetTranslation = (params: Record<string, any>, payload: Record<string, any>): void => {
  const {url} = payload;
  const {translations} = payload;

  Vue.http
    .post(url, {
      translations,
    })
    .then(
      () => {
        showGrowl('success', 'Translations successfully reset');
      },
      (error) => {
        showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
      },
    );
};

export const updatePageIndex = ({commit}: {commit: Commit}, pageIndex: string): void => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};

export const updateCurrentDomain = ({commit}: {commit: Commit}, currentDomain: string): void => {
  commit(types.SET_CURRENT_DOMAIN, currentDomain);
};

export const updatePrincipalLoading = ({commit}: {commit: Commit}, principalLoading: string): void => {
  commit(types.PRINCIPAL_LOADING, principalLoading);
};

export const updateSearch = ({commit}: {commit: Commit}, searchTags: Array<Record<string, any>>): void => {
  commit(types.SEARCH_TAGS, searchTags);
};
