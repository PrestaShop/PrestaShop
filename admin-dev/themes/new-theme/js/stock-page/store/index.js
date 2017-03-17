import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

// root state object.

const state = {
  products: [],
  hasQty: false,
  isReady: false
};

// mutations are operations that actually mutates the state.
// each mutation handler gets the entire state tree as the
// first argument, followed by additional payload arguments.
// mutations must be synchronous and can be recorded by plugins
// for debugging purposes.
const mutations = {
  addProducts(state, products) {
    state.products = [];
    state.products = products;
  },
  updateProduct(state, updatedProduct) {
    let index = window._.findIndex(state.products, {
      'product_id': updatedProduct.product_id,
      'product_attribute_id': updatedProduct.product_attribute_id
    });
    state.products.splice(index, 1, updatedProduct);
  },
  updateQty(state, payload) {
    let product = window._.find(state.products, {product_id: payload.productId});
    let hasQty = false;
    product.delta = payload.value;
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
  sort({commit, state}, payload) {
    let http = payload.http,
        url = payload.url,
        order = payload.column;

        http.get(url, {
          params: {
            order
          },
          emulateJSON: true
        }).then((res) => {
          commit('addProducts', res.body);
        }, function(error) {
            return window.$.growl.error({
              title:'',
              size: "large",
              message: error.statusText,
              duration: 3000
            });
        });
  },
  updateQtyByProductId({ commit, state }, payload) {
    let http = payload.http,
        url = payload.url,
        delta = payload.delta;

    http.post(url, {
      delta
    },
    {
      emulateJSON: true
    }).then((res) => {
      commit('addProducts', res.body);
      return window.$.growl.notice({
        title:'',
        size: "large",
        message: "Stock successfully updated",
        duration: 1000
      });
    }, function(error) {
        return window.$.growl.error({
          title:'',
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
