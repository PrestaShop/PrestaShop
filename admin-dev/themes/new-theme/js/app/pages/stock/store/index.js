/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Vue from 'vue';
import Vuex from 'vuex';
import * as actions from './actions';
import mutations from './mutations';
import _ from 'lodash';

Vue.use(Vuex);

// root state object.

const state = {
  order: '',
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
  suppliers(rootState) {
    function convert(suppliers) {
      suppliers.forEach((supplier) => {
        supplier.id = supplier.supplier_id;
      });
      return suppliers;
    }
    return convert(rootState.suppliers.data);
  },
  categories(rootState) {
    function convert(categories) {
      categories.forEach((category) => {
        category.children = _.values(category.children);
        rootState.categoryList.push(category);
        category.id = `${category.id_parent}-${category.id_category}`;
        convert(category.children);
      });
      return categories;
    }
    return convert(rootState.categories);
  },
  selectedProductsLng(rootState) {
    return rootState.selectedProducts.length;
  },
};

// A Vuex instance is created by combining the state, mutations, actions,
// and getters.
export default new Vuex.Store({
  state,
  getters,
  actions,
  mutations,
});
