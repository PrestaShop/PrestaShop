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
    state.domainsTree = domainsTree;
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
    state.currentDomain = currentDomain;
  },
};
