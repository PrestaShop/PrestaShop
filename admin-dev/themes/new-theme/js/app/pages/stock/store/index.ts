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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
/* eslint-disable no-param-reassign */

import {createStore} from 'vuex';
import _ from 'lodash';
import * as actions from './actions';
import mutations from './mutations';

// root state object.

const state = {
  order: '',
  sort: 'desc',
  pageIndex: 1,
  totalPages: 0,
  productsPerPage: 30,
  products: [],
  hasQty: false,
  keywords: [],
  suppliers: {
    data: [],
  },
  categories: [],
  categoryList: [],
  movements: [],
  employees: [],
  movementsTypes: [],
  translations: {},
  isLoading: false,
  isReady: false,
  editBulkUrl: '',
  bulkEditQty: null,
  productsToUpdate: [],
  selectedProducts: [],
};

// getters are functions
const getters = {
  suppliers(rootState: Record<string, any>) {
    function convert(suppliers: Record<string, any>) {
      suppliers.forEach((supplier: Record<string, any>) => {
        supplier.id = supplier.supplier_id;
      });
      return suppliers;
    }
    return convert(rootState.suppliers.data);
  },
  categories(rootState: Record<string, any>) {
    function convert(categories: Record<string, any>) {
      categories.forEach((category: Record<string, any>) => {
        category.children = _.values(category.children);
        rootState.categoryList.push(category);
        category.id = `${category.id_parent}-${category.id_category}`;
        convert(category.children);
      });
      return categories;
    }
    return convert(rootState.categories);
  },
  selectedProductsLng(rootState: Record<string, any>) {
    return rootState.selectedProducts.length;
  },
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default createStore({
  state() {
    return state;
  },
  getters,
  actions,
  mutations,
});
