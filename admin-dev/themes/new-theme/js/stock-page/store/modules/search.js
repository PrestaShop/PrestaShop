import * as types from '../mutation-types';
import { showGrowl } from '../utils/growl';

// initial state
const state = {

};

// getters
const getters = {

};

// actions
const actions = {
  searchByKeywords({ commit, state }, payload) {
    console.log(payload)
  }
};

// mutations
const mutations = {

};

export default {
  state,
  getters,
  actions,
  mutations
};