import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';

Vue.use(Vuex);

// root state object.

const state = {
  translations: {},
  catalog: {},
  domainsTree: {},
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
    return state.domainsTree;
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
