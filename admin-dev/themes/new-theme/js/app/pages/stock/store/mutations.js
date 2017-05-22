/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import * as types from './mutation-types';

export default {
  [types.UPDATE_ORDER](state, order) {
    state.order = order;
  },
  [types.UPDATE_KEYWORDS](state, keywords) {
    state.keywords = keywords;
  },
  [types.SET_TOTAL_PAGES](state, totalPages) {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state, pageIndex) {
    state.pageIndex = pageIndex;
  },
  [types.SET_SUPPLIERS](state, suppliers) {
    state.suppliers = suppliers;
  },
  [types.SET_CATEGORIES](state, categories) {
    state.categories = categories.data.tree.children;
  },
  [types.SET_MOVEMENTS](state, movements) {
    state.movements = movements.data;
  },
  [types.SET_TRANSLATIONS](state, translations) {
    translations.data.forEach((t) => {
      state.translations[t.translation_id] = t.name;
    });
  },
  [types.LOADING_STATE](state, isLoading) {
    state.isLoading = isLoading;
  },
  [types.APP_IS_READY](state) {
    state.isReady = true;
  },
  [types.SET_EMPLOYEES_LIST](state, employees) {
    state.employees = employees.data;
  },
  [types.SET_MOVEMENTS_TYPES](state, movementsTypes) {
    state.movementsTypes = movementsTypes.data;
  },
  [types.ADD_PRODUCTS](state, products) {
    _.forEach(products.data.data, (product) => {
      product.qty = 0;
    });
    state.editBulkUrl = products.data.info.edit_bulk_url;
    state.products = products.data.data;
  },
  [types.UPDATE_PRODUCT](state, updatedProduct) {
    const index = _.findIndex(state.products, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });
    updatedProduct.qty = 0;
    state.products.splice(index, 1, updatedProduct);
  },
  [types.UPDATE_PRODUCTS](state, updatedProducts) {
    state.productsToUpdate = [];
    _.forEach(updatedProducts, (product) => {
      const index = _.findIndex(state.products, {
        product_id: product.product_id,
        combination_id: product.combination_id,
      });
      product.qty = 0;
      state.products.splice(index, 1, product);
    });
    state.hasQty = false;
  },
  [types.UPDATE_PRODUCT_QTY](state, updatedProduct) {
    let hasQty = false;

    const index = _.findIndex(state.productsToUpdate, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });

    const productToUpdate = _.find(state.products, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });

    _.forEach(state.products, (product) => {
      productToUpdate.qty = updatedProduct.delta;
      if (product.qty) {
        hasQty = true;
      }
    });

    state.hasQty = hasQty;

    if (index !== -1) {
      return state.productsToUpdate.splice(index, 1, updatedProduct);
    }
    if (updatedProduct.delta) {
      state.productsToUpdate.push(updatedProduct);
    }
  },
};
