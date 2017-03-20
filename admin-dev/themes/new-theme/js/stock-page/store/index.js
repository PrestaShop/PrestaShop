import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';

Vue.use(Vuex);

// root state object.

const state = {
  products: [],
  hasQty: false
};

// getters are functions
const getters = {
  hasQty(state) {
    return state.hasQty;
  }
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations
});
