import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import _ from 'lodash';

Vue.use(Vuex);

// root state object.

const state = {
  pageIndex: 1,
  totalPages: 0,
  translationsPerPage: 20,
  translations: {
    data: {},
    info: {}
  },
  catalog: {
    data: {},
    info: {}
  },
  domainsTree: {
    data: {},
    info: {}
  },
  isReady: false,
};

// getters are functions
const getters = {
  totalPages(state) {
    return state.totalPages;
  },
  pageIndex(state) {
    return state.pageIndex;
  },
  translations(state) {
    return state.translations;
  },
  catalog(state) {
    return state.catalog;
  },
  domainsTree(state) {
    function convert(domains) {
      domains.forEach((domain)=>{
        domain.children = _.values(domain.children);
        domain.extraLabel = domain.total_missing_translations;
        domain.dataValue = domain.domain_catalog_link;
        domain.warning = Boolean(domain.total_missing_translations);
        domain.id = domain.full_name;
        convert(domain.children);
      });
      return domains;
    }

    return convert(_.values(state.domainsTree.data));
  },
  isReady(state) {
    return state.isReady;
  }
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations,
});
