/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
