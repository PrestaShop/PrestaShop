import * as types from './mutation-types';

export default {
  [types.ADD_PRODUCTS] (state, products) {
    window._.forEach(products, (product) => {
      product.qty = 0;
    });
    state.products = products;
  },
  [types.UPDATE_ORDER] (state, order) {
    state.order = order;
  },
  [types.UPDATE_PRODUCT] (state, updatedProduct) {
    let index = window._.findIndex(state.products, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });
    updatedProduct.qty = 0;
    state.products.splice(index, 1, updatedProduct);
  },
  [types.UPDATE_PRODUCTS] (state, updatedProducts) {
    state.productsToUpdate = [];
    window._.forEach(updatedProducts, (product) => {
      let index = window._.findIndex(state.products, {
        'product_id': product.product_id,
        'combination_id': product.combination_id
      });
      product.qty = 0;
      state.products.splice(index, 1, product);
    });
    state.hasQty = false;
  },
  [types.UPDATE_PRODUCT_QTY] (state, updatedProduct) {
    let hasQty = false;

    let index = window._.findIndex(state.productsToUpdate, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });

    let productToUpdate = window._.find(state.products, {
      'product_id': updatedProduct.product_id,
      'combination_id': updatedProduct.combination_id
    });

    window._.forEach(state.products, (product) => {
      productToUpdate.qty = updatedProduct.delta;
      if(product.qty) {
        hasQty = true;
      }
    });

    state.hasQty = hasQty;

    if(index !== -1) {
      return state.productsToUpdate.splice(index, 1, updatedProduct);
    }

    state.productsToUpdate.push(updatedProduct);
  },
  [types.SET_TOTAL_PAGES] (state, totalPages) {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX] (state, pageIndex) {
    state.pageIndex = pageIndex;
  }
};
