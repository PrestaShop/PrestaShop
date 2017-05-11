import * as types from './mutation-types';

export default {
  [types.UPDATE_ORDER](state, order) {
    state.order = order;
  },
  [types.UPDATE_KEYWORDS](state, keywords) {
    state.keywords = keywords;
  },
  [types.SET_TOTAL_PAGES](state, totalPages) {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state, pageIndex) {
    state.pageIndex = pageIndex;
  },
  [types.SET_SUPPLIERS](state, suppliers) {
    state.suppliers = suppliers;
  },
  [types.SET_CATEGORIES](state, categories) {
    state.categories = categories;
  },
  [types.SET_MOVEMENTS](state, movements) {
    state.movements = movements;
  },
  [types.SET_TRANSLATIONS](state, translations) {
    state.translations = translations;
  },
  [types.LOADING_STATE](state, isLoading) {
    state.isLoading = isLoading;
  },
  [types.APP_IS_READY](state) {
    state.isReady = true;
  },
  [types.SET_EMPLOYEES_LIST](state, employees) {
    state.employees = employees;
  },
  [types.SET_MOVEMENTS_TYPES](state, movementsTypes) {
    state.movementsTypes = movementsTypes;
  }
};
