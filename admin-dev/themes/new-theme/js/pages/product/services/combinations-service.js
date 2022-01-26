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

import Router from '@components/router';

const {$} = window;

export default class CombinationsService {
  constructor() {
    this.router = new Router();
    this.filters = {};
    this.orderBy = null;
    this.orderWay = null;
  }

  /**
   * @param {int} combinationId
   *
   * @returns {Promise}
   */
  deleteCombination(combinationId) {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_delete_combination', {
        combinationId,
      }),
      type: 'DELETE',
    });
  }

  /**
   * @param {Number} combinationId
   * @param {Object} data
   *
   * @returns {Promise}
   */
  updateListedCombination(combinationId, data) {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_update_combination_from_listing', {
        combinationId,
      }),
      data,
      type: 'PATCH',
    });
  }

  /**
   * @param {Number} productId
   * @param {Object} data Attributes indexed by attributeGroupId { 1: [23, 34], 3: [45, 52]}
   */
  generateCombinations(productId, data) {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_generate', {
        productId,
      }),
      data,
      method: 'POST',
    });
  }

  /**
   * It actually updates one combination at a time, but calls api designed for bulk update purpose
   *  (same form applied to multiple combinations one by one)
   *
   * @param {number} combinationId
   * @param {Object} data
   *
   * @returns {Promise}
   */
  bulkUpdate(combinationId, data) {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_bulk_edit_combination', {combinationId}),
      data,
      method: 'POST',
    });
  }

  /**
   * @param {Number} productId
   *
   * @returns {Promise}
   */
  getCombinationIds(productId) {
    return $.get(
      this.router.generate('admin_products_combinations_ids', {
        productId,
      }),
    );
  }
}
