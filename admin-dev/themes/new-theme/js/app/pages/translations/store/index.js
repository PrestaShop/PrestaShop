import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import _ from 'lodash';

Vue.use(Vuex);

// root state object.

const state = {
  translations: {},
  catalog: {},
  domainsTree: [],
};

// getters are functions
const getters = {
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

    return convert(_.values(state.domainsTree));
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
