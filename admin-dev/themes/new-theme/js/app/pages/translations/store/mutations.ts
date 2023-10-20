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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
/* eslint-disable no-param-reassign */
import * as types from './mutation-types';

export default {
  [types.SET_TRANSLATIONS](state: Record<string, any>, translations: Record<string, any>): void {
    translations.data.forEach((t: Record<string, any>) => {
      state.translations[t.translation_id] = t.name;
    });
  },
  [types.SET_CATALOG](state: Record<string, any>, catalog: Record<string, any>): void {
    state.catalog = catalog;
  },
  [types.SET_DOMAINS_TREE](state: Record<string, any>, domainsTree: Record<string, any>): void {
    state.totalMissingTranslations = domainsTree.data.tree.total_missing_translations;
    state.totalTranslations = domainsTree.data.tree.total_translations;
    state.domainsTree = domainsTree.data.tree.children;
  },
  [types.APP_IS_READY](state: Record<string, any>): void {
    state.isReady = true;
  },
  [types.SET_TOTAL_PAGES](state: Record<string, any>, totalPages: number): void {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state: Record<string, any>, pageIndex: string): void {
    state.pageIndex = pageIndex;
  },
  [types.SET_CURRENT_DOMAIN](state: Record<string, any>, currentDomain: Record<string, any>): void {
    state.currentDomain = currentDomain.full_name;
    state.currentDomainTotalTranslations = currentDomain.total_translations;
    state.currentDomainTotalMissingTranslations = currentDomain.total_missing_translations;
  },
  [types.RESET_CURRENT_DOMAIN](state: Record<string, any>): void {
    state.currentDomain = '';
    state.currentDomainTotalTranslations = 0;
    state.currentDomainTotalMissingTranslations = 0;
  },
  [types.SIDEBAR_LOADING](state: Record<string, any>, isLoading: boolean): void {
    state.sidebarLoading = isLoading;
  },
  [types.PRINCIPAL_LOADING](state: Record<string, any>, isLoading: boolean): void {
    state.principalLoading = isLoading;
  },
  [types.SEARCH_TAGS](state: Record<string, any>, searchTags: Array<Record<string, any>>): void {
    state.searchTags = searchTags;
  },
  [types.DECREASE_CURRENT_DOMAIN_TOTAL_MISSING_TRANSLATIONS](state: Record<string, any>, successfullySaved: number): void {
    state.currentDomainTotalMissingTranslations -= successfullySaved;
  },
  [types.RESET_MODIFIED_TRANSLATIONS](state: Record<string, any>): void {
    state.modifiedTranslations = [];
  },
};
