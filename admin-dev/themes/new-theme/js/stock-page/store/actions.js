import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';

Vue.use(VueResource);

export const sort = ({ commit, state }, payload) => {
  let url = payload.url,
      order = payload.order;

  Vue.http.get(url, {
    params: {
      order
    }
  }).then((res) => {
    commit(types.ADD_PRODUCTS, res.body);
  }, function(error) {
      return showGrowl('error', error.statusText);
  });
};

export const updateQtyByProductId = ({ commit, state }, payload) => {
  let url = payload.url,
      delta = payload.delta;

  Vue.http.post(url, {
    delta
  }).then((res) => {
      commit(types.UPDATE_PRODUCT, res.body);
      return showGrowl('notice', 'Stock successfully updated');
  }, function(error) {
      return showGrowl('error', error.statusText);
  });
};

export const updateQtyByProductsId = ({ commit, state }, payload) => {
  let url = payload.url,
      productsQty = state.productsToUpdate;

  Vue.http.post(url, productsQty).then((res) => {
      commit(types.UPDATE_PRODUCTS, res.body);
      return showGrowl('notice', 'Stock successfully updated');
  }, function(error) {
      return showGrowl('error', error.statusText);
  });
};

export const getStock = ({ commit, state }, payload) => {
  Vue.http.get(payload.url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index
    }
  }).then(function(response){
    if(response.status === 200) {
      commit(types.ADD_PRODUCTS, response.body);
    }
  }, function(error){
      return showGrowl('error', error.statusText);
  });
};

const showGrowl = (type, message) => {
  window.$.growl[type]({
    title:'',
    size: "large",
    message: message,
    duration: 1000
  });
};
