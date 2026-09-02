/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
