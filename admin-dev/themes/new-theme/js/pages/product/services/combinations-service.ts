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
  router: Router;

  filters: Record<string, any>;

  orderBy: string | null;

  orderWay: string | null;

  constructor() {
    this.router = new Router();
    this.filters = {};
    this.orderBy = null;
    this.orderWay = null;
  }

  deleteCombination(combinationId: number): JQuery.jqXHR<any> {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_delete_combination', {
        combinationId,
      }),
      type: 'DELETE',
    });
  }

  bulkDeleteCombinations(productId: number, combinationIds: number[], abortSignal: AbortSignal): Promise<Response> {
    const formData = new FormData();
    formData.append('combinationIds', JSON.stringify(combinationIds));

    return fetch(
      this.router.generate('admin_products_combinations_bulk_delete', {productId}),
      {
        method: 'POST',
        body: formData,
        signal: abortSignal,
      },
    );
  }

  updateCombinationList(productId: number, formData: FormData): Promise<Response> {
    formData.append('_method', 'PATCH');

    return fetch(
      this.router.generate('admin_products_combinations_update_combination_from_listing', {productId}),
      {
        method: 'POST',
        body: formData,
        headers: {
          _method: 'PATCH',
        },
      },
    );
  }

  /**
   * @param {number} productId
   * @param {Record<number, number[]>} data Attributes indexed by attributeGroupId { 1: [23, 34], 3: [45, 52]}
   */
  generateCombinations(productId: number, data: Record<number, number[]>): JQuery.jqXHR {
    return $.ajax({
      url: this.router.generate('admin_products_combinations_generate', {
        productId,
      }),
      data,
      method: 'POST',
    });
  }

  bulkUpdate(productId: number, combinationIds: number[], formData: FormData, abortSignal: AbortSignal): Promise<Response> {
    formData.append('_method', 'PATCH');
    formData.append('combinationIds', JSON.stringify(combinationIds));

    return fetch(this.router.generate('admin_products_combinations_bulk_edit_combination',
      {
        productId,
      }), {
      method: 'POST',
      body: formData,
      headers: {
        _method: 'PATCH',
      },
      signal: abortSignal,
    });
  }
}
