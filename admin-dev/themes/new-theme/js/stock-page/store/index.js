import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

// root state object.

const state = {
  products: []
}

// mutations are operations that actually mutates the state.
// each mutation handler gets the entire state tree as the
// first argument, followed by additional payload arguments.
// mutations must be synchronous and can be recorded by plugins
// for debugging purposes.
const mutations = {
  addProducts(state, products) {
    state.products = products;
  },
  updateQty(state, payload) {
    let product = _.find(state.products, {product_id: payload.productId});
    product.qty = payload.value;
  }
}

// actions are functions that causes side effects and can involve
// asynchronous operations.
const actions = {
  updateQtyByProductId(state, payload) {
    let http = payload.http,
        url = payload.url,
        quantity = payload.qty;

    http.post(url, {
      quantity
    },
    {
      emulateJSON: true
    }).then(function(res){
      //TODO
      console.log(res);
    }, function(error){
        console.log(error.statusText);
    });
  }
}

// getters are functions
const getters = {

}

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations
})
