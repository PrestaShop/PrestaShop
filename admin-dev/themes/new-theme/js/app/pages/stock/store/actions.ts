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
import {Commit} from 'vuex/types';
import * as types from '@app/pages/stock/store/mutation-types';
import {showGrowl} from '@app/utils/growl';
import {EventEmitter} from '@components/event-emitter';
import {
  omitBy, isNil,
} from 'lodash';

const isParamInvalid = (value: any) => isNil(value) || value.length <= 0;

export const getStock = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const url = window.data.apiStockUrl;
  const params = new URLSearchParams(omitBy({
    order: payload.order,
    page_size: payload.page_size,
    page_index: payload.page_index,
    keywords: payload.keywords,
    active: payload.active,
    low_stock: payload.low_stock,
  }, isParamInvalid));

  if (payload.suppliers) {
    payload.suppliers.forEach((v: string) => params.append('supplier_id[]', v));
  }
  if (payload.categories) {
    payload.categories.forEach((v: string) => params.append('category_id[]', v));
  }
  const fetchUrl = `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;

  try {
    const response = await fetch(fetchUrl);
    const datas = await response.json();

    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.ADD_PRODUCTS, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getSuppliers = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.suppliersUrl;

  try {
    const response = await fetch(url);
    const datas = await response.json();
    commit(types.SET_SUPPLIERS, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getCategories = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.categoriesUrl;

  try {
    const response = await fetch(url);
    const datas = await response.json();
    commit(types.SET_CATEGORIES, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getMovements = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const url = window.data.apiMovementsUrl;
  const params = new URLSearchParams(omitBy({
    order: payload.order,
    page_size: payload.page_size,
    page_index: payload.page_index,
    keywords: payload.keywords,
    supplier_id: payload.suppliers,
    category_id: payload.categories,
    id_stock_mvt_reason: payload.id_stock_mvt_reason,
    id_employee: payload.id_employee,
  }, isParamInvalid));

  if (payload.date_add?.sup) {
    params.append('date_add[sup]', payload.date_add.sup);
  }

  if (payload.date_add?.inf) {
    params.append('date_add[inf]', payload.date_add.inf);
  }

  const fetchUrl = `${url}${url.includes('?') ? '&' : '?'}${params.toString()}`;

  try {
    const response = await fetch(fetchUrl);
    const datas = await response.json();

    commit(types.LOADING_STATE, false);
    commit(types.SET_TOTAL_PAGES, response.headers.get('Total-Pages'));
    commit(types.SET_MOVEMENTS, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getTranslations = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.translationUrl;
  try {
    const response = await fetch(url);
    const datas = await response.json();

    commit(types.SET_TRANSLATIONS, datas);
    commit(types.APP_IS_READY);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getEmployees = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.employeesUrl;
  try {
    const response = await fetch(url);
    const datas = await response.json();
    commit(types.SET_EMPLOYEES_LIST, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const getMovementsTypes = async ({commit}: {commit: Commit}): Promise<void> => {
  const url = window.data.movementsTypesUrl;
  try {
    const response = await fetch(url);
    const datas = await response.json();
    commit(types.SET_MOVEMENTS_TYPES, datas);
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const updateOrder = ({commit}: {commit: Commit}, order: Record<string, any>): void => {
  commit(types.UPDATE_ORDER, order);
};

export const updateSort = ({commit}: {commit: Commit}, sort: string): void => {
  commit(types.UPDATE_SORT, sort);
};

export const updatePageIndex = ({commit}: {commit: Commit}, pageIndex: number): void => {
  commit(types.SET_PAGE_INDEX, pageIndex);
};

export const updateKeywords = ({commit}: {commit: Commit}, keywords: Array<string>): void => {
  commit(types.UPDATE_KEYWORDS, keywords);
};

export const isLoading = ({commit}: {commit: Commit}): void => {
  commit(types.LOADING_STATE, true);
};

export const updateProductQty = ({commit}: {commit: Commit}, payload: Record<string, any>): void => {
  commit(types.UPDATE_PRODUCT_QTY, payload);
};

export const updateQtyByProductId = async ({commit}: {commit: Commit}, payload: Record<string, any>): Promise<void> => {
  const {url} = payload;
  const {delta} = payload;

  try {
    const res = await fetch(url, {
      method: 'POST',
      body: JSON.stringify({delta}),
    });
    const datas = await res.json();

    commit(types.UPDATE_PRODUCT, datas);
    EventEmitter.emit('displayBulkAlert', 'success');
  } catch (error: any) {
    showGrowl('error', error.statusText);
  }
};

export const updateQtyByProductsId = async ({commit, state}: {commit: Commit, state: Record<string, any>}): Promise<void> => {
  const url = state.editBulkUrl;
  const productsQty = state.productsToUpdate;

  try {
    const res = await fetch(url, {
      method: 'POST',
      body: JSON.stringify(productsQty),
    });
    const datas = await res.json();

    commit(types.UPDATE_PRODUCTS_QTY, datas);
    EventEmitter.emit('displayBulkAlert', 'success');
  } catch (error: any) {
    showGrowl('error', error.body?.error ?? error.statusText);
  }
};

export const updateBulkEditQty = ({commit}: {commit: Commit}, value: number): void => {
  commit(types.UPDATE_BULK_EDIT_QTY, value);
};

export const addProductToUpdate = ({commit}: {commit: Commit}, product: Record<string, any>): void => {
  commit(types.ADD_PRODUCT_TO_UPDATE, product);
};

export const removeProductToUpdate = ({commit}: {commit: Commit}, product: Record<string, any>): void => {
  commit(types.REMOVE_PRODUCT_TO_UPDATE, product);
};

export const addSelectedProduct = ({commit}: {commit: Commit}, product: Record<string, any>): void => {
  commit(types.ADD_SELECTED_PRODUCT, product);
};

export const removeSelectedProduct = ({commit}: {commit: Commit}, product: Record<string, any>): void => {
  commit(types.REMOVE_SELECTED_PRODUCT, product);
};
