import * as types from './mutation-types';

export default {
  [types.SET_TRANSLATIONS](state, translations) {
    state.translations = translations;
  },
};
