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
    state.categories = categories;
  },
  [types.SET_MOVEMENTS](state, movements) {
    state.movements = movements;
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
    state.employees = employees;
  },
  [types.SET_MOVEMENTS_TYPES](state, movementsTypes) {
    state.movementsTypes = movementsTypes;
  },
};
