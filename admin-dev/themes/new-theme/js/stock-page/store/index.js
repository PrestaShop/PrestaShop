import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// root state object.

const state = {
  products: [],
  hasQty: false
};

// mutations are operations that actually mutates the state.
// each mutation handler gets the entire state tree as the
// first argument, followed by additional payload arguments.
// mutations must be synchronous and can be recorded by plugins
// for debugging purposes.
const mutations = {
  addProducts(state, products) {
    state.products = products;
  },
  updateProduct(state, product) {
    // TODO
  },
  updateQty(state, payload) {
    let product = window._.find(state.products, {product_id: payload.productId});
    let hasQty = false;
    product.qty = payload.value;
    state.products.filter((product)=> {
      if(product.qty !== 0) {
        hasQty = true;
      }
    });
    state.hasQty = hasQty;
  }
};

// actions are functions that causes side effects and can involve
// asynchronous operations.
const actions = {
  updateQtyByProductId({ commit, state }, payload) {
    let http = payload.http,
        url = payload.url,
        quantity = payload.qty;

    http.post(url, {
      quantity
    },
    {
      emulateJSON: true
    }).then((res) => {
      commit('updateProduct', res.body);
      return window.$.growl.notice({
        title:'',
        fixed: true,
        size: "large",
        message: "Stock successfully updated",
        duration: 3000
      });
    }, function(error){
        return window.$.growl.error({
          title:'',
          fixed: true,
          size: "large",
          message: error.statusText,
          duration: 3000
        });
    });
  }
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
