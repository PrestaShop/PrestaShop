/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from 'app/utils/growl';
import { EventBus } from 'app/utils/event-bus';
import _ from 'lodash';

Vue.use(VueResource);

export const getStock = ({ commit }, payload) => {
  const url = window.data.apiStockUrl;
  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : [],
      category_id: payload.categories ? payload.categories : [],
      active: payload.active !== 'null' ? payload.active : [],
      low_stock: payload.low_stock,
    },
  }).then((response) => {
    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.ADD_PRODUCTS, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getSuppliers = ({ commit }) => {
  const url = window.data.suppliersUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_SUPPLIERS, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getCategories = ({ commit }) => {
  const url = window.data.categoriesUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_CATEGORIES, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getMovements = ({ commit }, payload) => {
  const url = window.data.apiMovementsUrl;

  Vue.http.get(url, {
    params: {
      order: payload.order,
      page_size: payload.page_size,
      page_index: payload.page_index,
      keywords: payload.keywords ? payload.keywords : [],
      supplier_id: payload.suppliers ? payload.suppliers : [],
      category_id: payload.categories ? payload.categories : [],
      id_stock_mvt_reason: payload.id_stock_mvt_reason ? payload.id_stock_mvt_reason : [],
      id_employee: payload.id_employee ? payload.id_employee : [],
      date_add: payload.date_add ? payload.date_add : [],
    },
  }).then((response) => {
    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_MOVEMENTS, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getTranslations = ({ commit }) => {
  const url = window.data.translationUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_TRANSLATIONS, response.body);
    commit(types.APP_IS_READY);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getEmployees = ({ commit }) => {
  const url = window.data.employeesUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_EMPLOYEES_LIST, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const getMovementsTypes = ({ commit }) => {
  const url = window.data.movementsTypesUrl;
  Vue.http.get(url).then((response) => {
    commit(types.SET_MOVEMENTS_TYPES, response.body);
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const updateOrder = ({ commit }, order) => {
  commit(types.UPDATE_ORDER, order);
};

export const updatePageIndex = ({ commit }, pageIndex) => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};

export const updateKeywords = ({ commit }, keywords) => {
  commit(types.UPDATE_KEYWORDS, keywords);
};

export const isLoading = ({ commit }) => {
  commit(types.LOADING_STATE, true);
};

export const updateProductQty = ({ commit }, payload) => {
  commit(types.UPDATE_PRODUCT_QTY, payload);
};

export const updateQtyByProductId = ({ commit, state }, payload) => {
  const url = payload.url;
  const delta = payload.delta;

  Vue.http.post(url, {
    delta,
  }).then((res) => {
    commit(types.UPDATE_PRODUCT, res.body);
    EventBus.$emit('displayBulkAlert', 'success');
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const updateQtyByProductsId = ({ commit, state }) => {
  const url = state.editBulkUrl;
  const productsQty = state.productsToUpdate;

  Vue.http.post(url, productsQty).then((res) => {
    commit(types.UPDATE_PRODUCTS_QTY, res.body);
    EventBus.$emit('displayBulkAlert', 'success');
  }, (error) => {
    showGrowl('error', error.statusText);
  });
};

export const updateBulkEditQty = ({ commit }, value) => {
  commit(types.UPDATE_BULK_EDIT_QTY, value);
};

export const addProductToUpdate = ({ commit }, product) => {
  commit(types.ADD_PRODUCT_TO_UPDATE, product);
};

export const removeProductToUpdate = ({ commit }, product) => {
  commit(types.REMOVE_PRODUCT_TO_UPDATE, product);
};

export const addSelectedProduct = ({ commit }, product) => {
  commit(types.ADD_SELECTED_PRODUCT, product);
};

export const removeSelectedProduct = ({ commit }, product) => {
  commit(types.REMOVE_SELECTED_PRODUCT, product);
};
