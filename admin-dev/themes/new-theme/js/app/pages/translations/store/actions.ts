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
import {Commit} from 'vuex/types';
import * as types from '@app/pages/translations/store/mutation-types';
import {showGrowl} from '@app/utils/growl';
import {
  omitBy, isNil,
} from 'lodash';

const isParamInvalid = (value: any) => isNil(value) || value.length <= 0;

export const getTranslations = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.translationUrl;

  try {
    const response = await fetch(url);
    const datas = await response.json();
    commit(types.SET_TRANSLATIONS, datas);
    commit(types.APP_IS_READY);
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
};

export const getCatalog = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  commit(types.PRINCIPAL_LOADING, true);

  try {
    const response = await fetch(`${payload.url}&${new URLSearchParams(omitBy({
      page_size: payload.page_size,
      page_index: payload.page_index,
    }, isParamInvalid))}`);
    const datas = await response.json();

    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_CATALOG, datas);
    commit(types.PRINCIPAL_LOADING, false);
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
};

export const getDomainsTree = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const url = window.data.domainsTreeUrl;
  const params = new URLSearchParams();

  commit(types.SIDEBAR_LOADING, true);
  commit(types.PRINCIPAL_LOADING, true);

  if (payload.store.getters.searchTags.length) {
    payload.store.getters.searchTags.forEach((searchTag: string) => {
      params.append('search[]', searchTag);
    });
  }

  const fetchUrl = `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;

  try {
    const response = await fetch(fetchUrl);
    const datas = await response.json();

    commit(types.SET_DOMAINS_TREE, datas);
    commit(types.SIDEBAR_LOADING, false);
    commit(types.RESET_CURRENT_DOMAIN);
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
};

export const refreshCounts = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const url = window.data.domainsTreeUrl;
  const params = new URLSearchParams();

  if (payload.store.getters.searchTags.length) {
    payload.store.getters.searchTags.forEach((searchTag: string) => {
      params.append('search[]', searchTag);
    });
  }
  const fetchUrl = `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;

  try {
    const response = await fetch(fetchUrl);
    const datas = await response.json();

    commit(types.DECREASE_CURRENT_DOMAIN_TOTAL_MISSING_TRANSLATIONS, payload.successfullySaved);
    commit(types.SET_DOMAINS_TREE, datas);
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
};

export const saveTranslations = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const {url} = payload;
  const {translations} = payload;

  try {
    await fetch(url, {
      method: 'POST',
      body: JSON.stringify({translations}),
    });

    payload.store.dispatch('refreshCounts', {
      successfullySaved: translations.length,
      store: payload.store,
    });
    commit(types.RESET_MODIFIED_TRANSLATIONS);
    showGrowl('success', 'Translations successfully updated');
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
};

/* eslint-disable-next-line no-unused-vars */
export const resetTranslation = async (params: Record<string, any>, payload: Record<string, any>): Promise<void> => {
  const {url} = payload;
  const {translations} = payload;

  try {
    await fetch(url, {
      method: 'POST',
      body: JSON.stringify({translations}),
    });

    showGrowl('success', 'Translations successfully reset');
  } catch (error: any) {
    showGrowl('error', error.bodyText ? JSON.parse(error.bodyText).error : error.statusText);
  }
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
