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
import * as types from './mutation-types';

export default {
  [types.SET_TRANSLATIONS](state, translations) {
    translations.data.forEach((t) => {
      state.translations[t.translation_id] = t.name;
    });
  },
  [types.SET_CATALOG](state, catalog) {
    state.catalog = catalog;
  },
  [types.SET_DOMAINS_TREE](state, domainsTree) {
    state.totalMissingTranslations = domainsTree.data.tree.total_missing_translations;
    state.totalTranslations = domainsTree.data.tree.total_translations;
    state.domainsTree = domainsTree.data.tree.children;
  },
  [types.APP_IS_READY](state) {
    state.isReady = true;
  },
  [types.SET_TOTAL_PAGES](state, totalPages) {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state, pageIndex) {
    state.pageIndex = pageIndex;
  },
  [types.SET_CURRENT_DOMAIN](state, currentDomain) {
    state.currentDomain = currentDomain.full_name;
    state.currentDomainTotalTranslations = currentDomain.total_translations;
    state.currentDomainTotalMissingTranslations = currentDomain.total_missing_translations;
  },
  [types.RESET_CURRENT_DOMAIN](state) {
    state.currentDomain = '';
    state.currentDomainTotalTranslations = 0;
    state.currentDomainTotalMissingTranslations = 0;
  },
  [types.SIDEBAR_LOADING](state, isLoading) {
    state.sidebarLoading = isLoading;
  },
  [types.PRINCIPAL_LOADING](state, isLoading) {
    state.principalLoading = isLoading;
  },
  [types.SEARCH_TAGS](state, searchTags) {
    state.searchTags = searchTags;
  },
};
