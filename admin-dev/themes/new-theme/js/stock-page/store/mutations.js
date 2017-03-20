import * as types from './mutation-types';

export default {
  [types.ADD_PRODUCTS] (state, products) {
    state.products = products;
  },
  [types.UPDATE_PRODUCTS] (state, updatedProduct) {
    let index = window._.findIndex(state.products, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });
    state.products.splice(index, 1, updatedProduct);
  },
  [types.UPDATE_PRODUCT_QTY] (state) {
    let hasQty = false;
    window._.forEach(state.products, (product) => {
      if(product.qty) {
        hasQty = true;
      }
    });
    state.hasQty = hasQty;
  }
};
