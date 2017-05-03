import * as types from './mutation-types';

export default {
  [types.SET_TRANSLATIONS](state, translations) {
    state.translations = translations;
  },
  [types.SET_CATALOG](state, catalog) {
    state.catalog = catalog;
  },
  [types.SET_DOMAINS_TREE](state, domainsTree) {
    state.domainsTree = domainsTree;
  },
};
