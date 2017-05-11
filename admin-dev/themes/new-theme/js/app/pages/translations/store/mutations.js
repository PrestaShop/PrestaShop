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
};
