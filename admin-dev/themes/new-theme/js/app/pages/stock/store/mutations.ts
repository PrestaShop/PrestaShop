/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/* eslint-disable no-param-reassign */

import _ from 'lodash';
import * as types from './mutation-types';

export default {
  [types.UPDATE_ORDER](state: Record<string, any>, order: Record<string, any>): void {
    state.order = order;
  },
  [types.UPDATE_SORT](state: Record<string, any>, sort: string): void {
    state.sort = sort;
  },
  [types.UPDATE_KEYWORDS](state: Record<string, any>, keywords: Array<string>): void {
    state.keywords = keywords;
  },
  [types.SET_TOTAL_PAGES](state: Record<string, any>, totalPages: number): void {
    state.totalPages = Number(totalPages);
  },
  [types.SET_PAGE_INDEX](state: Record<string, any>, pageIndex: number): void {
    state.pageIndex = pageIndex;
  },
  [types.SET_SUPPLIERS](state: Record<string, any>, suppliers: Array<Record<string, any>>): void {
    state.suppliers = suppliers;
  },
  [types.SET_CATEGORIES](state: Record<string, any>, categories: Record<string, any>): void {
    state.categories = categories.data.tree.children;
  },
  [types.SET_MOVEMENTS](state: Record<string, any>, movements: Record<string, any>): void {
    state.movements = movements.data;
  },
  [types.SET_TRANSLATIONS](state: Record<string, any>, translations: Record<string, any>): void {
    translations.data.forEach((t: Record<string, any>) => {
      state.translations[t.translation_id] = t.name;
    });
  },
  [types.LOADING_STATE](state: Record<string, any>, isLoading: boolean): void {
    state.isLoading = isLoading;
  },
  [types.APP_IS_READY](state: Record<string, any>): void {
    state.isReady = true;
  },
  [types.SET_EMPLOYEES_LIST](state: Record<string, any>, employees: Record<string, any>): void {
    state.employees = employees.data;
  },
  [types.SET_MOVEMENTS_TYPES](state: Record<string, any>, movementsTypes: Record<string, any>): void {
    state.movementsTypes = movementsTypes.data;
  },
  [types.ADD_PRODUCTS](state: Record<string, any>, products: Record<string, any>): void {
    state.productsToUpdate = [];
    state.selectedProducts = [];
    _.forEach(products.data.data, (product) => {
      product.qty = 0;
    });
    state.editBulkUrl = products.data.info.edit_bulk_url;
    state.products = products.data.data;
  },
  [types.UPDATE_PRODUCT](state: Record<string, any>, updatedProduct: Record<string, any>): void {
    const index = _.findIndex(state.products, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });
    const updatedIndex = _.findIndex(state.productsToUpdate, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });
    updatedProduct.qty = 0;
    state.products.splice(index, 1, updatedProduct);
    state.productsToUpdate.splice(updatedIndex, 1);
  },
  [types.UPDATE_PRODUCTS_QTY](state: Record<string, any>, updatedProducts: Record<string, any>): void {
    state.productsToUpdate = [];
    state.selectedProducts = [];
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
  [types.UPDATE_PRODUCT_QTY](state: Record<string, any>, updatedProduct: Record<string, any>): void {
    let hasQty = false;

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
  },
  [types.ADD_PRODUCT_TO_UPDATE](state: Record<string, any>, updatedProduct: Record<string, any>): void {
    const index = _.findIndex(state.productsToUpdate, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });

    if (index !== -1) {
      state.productsToUpdate.splice(index, 1, updatedProduct);
    } else {
      state.productsToUpdate.push(updatedProduct);
    }
  },
  [types.REMOVE_PRODUCT_TO_UPDATE](state: Record<string, any>, updatedProduct: Record<string, any>): void {
    const index = _.findIndex(state.productsToUpdate, {
      product_id: updatedProduct.product_id,
      combination_id: updatedProduct.combination_id,
    });
    state.productsToUpdate.splice(index, 1);
  },
  [types.UPDATE_BULK_EDIT_QTY](state: Record<string, any>, value: number): void {
    state.bulkEditQty = value;
    if (value) {
      _.forEach(state.selectedProducts, (product: Record<string, any>) => {
        const index = _.findIndex(state.productsToUpdate, {
          product_id: product.product_id,
          combination_id: product.combination_id,
        });
        product.qty = value;
        product.delta = state.bulkEditQty;
        if (index !== -1) {
          state.productsToUpdate.splice(index, 1, product);
        } else {
          state.productsToUpdate.push(product);
        }
      });
      state.hasQty = true;
    }
    if (value === null) {
      _.forEach(state.selectedProducts, (product) => {
        product.qty = 0;
      });
      state.productsToUpdate = [];
      state.selectedProducts = [];
      state.hasQty = false;
    }
  },
  [types.ADD_SELECTED_PRODUCT](state: Record<string, any>, product: Record<string, any>): void {
    const index = _.findIndex(state.selectedProducts, {
      product_id: product.product_id,
      combination_id: product.combination_id,
    });

    if (index !== -1) {
      state.selectedProducts.splice(index, 1, product);
    } else {
      state.selectedProducts.push(product);
    }
  },
  [types.REMOVE_SELECTED_PRODUCT](state: Record<string, any>, product: Record<string, any>): void {
    const index = _.findIndex(state.selectedProducts, {
      product_id: product.product_id,
      combination_id: product.combination_id,
    });

    if (index !== -1) {
      state.selectedProducts[index].qty = 0;
    }
    state.selectedProducts.splice(index, 1);
  },
};
