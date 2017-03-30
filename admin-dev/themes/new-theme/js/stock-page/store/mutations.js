import * as types from './mutation-types';

export default {
  [types.UPDATE_ORDER](state, order) {
    state.order = order;
  },
  [types.SET_TOTAL_PAGES](state, totalPages) {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state, pageIndex) {
    state.pageIndex = pageIndex;
  }
};