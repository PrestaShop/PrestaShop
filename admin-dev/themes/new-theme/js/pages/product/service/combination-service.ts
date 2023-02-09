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

const router = new Router();
const {$} = window;

export const deleteCombination = async (combinationId: number, shopId: number|null): Promise<JQuery.jqXHR> => {
  const routeParams: Record<string, unknown> = {combinationId};

  if (shopId !== null) {
    routeParams.shopId = shopId;
  }

  return $.ajax({
    url: router.generate('admin_products_combinations_delete_combination', routeParams),
    type: 'DELETE',
  });
};

export const bulkDeleteCombinations = async (
  productId: number,
  combinationIds: number[],
  shopId: number|null,
  abortSignal: AbortSignal,
): Promise<Response> => {
  const formData = new FormData();
  const routeParams:Record<string, unknown> = {productId};
  formData.append('combinationIds', JSON.stringify(combinationIds));

  if (shopId !== null) {
    routeParams.shopId = shopId;
  }

  return fetch(
    router.generate('admin_products_combinations_bulk_delete', routeParams), {
      method: 'POST',
      body: formData,
      signal: abortSignal,
    },
  );
};

export const updateCombinationList = async (productId: number, formData: FormData): Promise<Response> => {
  formData.append('_method', 'PATCH');

  return fetch(
    router.generate('admin_products_combinations_update_combination_from_listing', {productId}),
    {
      method: 'POST',
      body: formData,
      headers: {
        _method: 'PATCH',
      },
    },
  );
};

/**
 * @param {number} productId
 * @param {number|null} shopId
 * @param {Record<number, number[]>} data Attributes indexed by attributeGroupId { 1: [23, 34], 3: [45, 52]}
 */
export const generateCombinations = async (
  productId: number,
  shopId: number|null,
  data: Record<number, number[]>,
): Promise<JQuery.jqXHR> => {
  const routeParams = <Record<string, number>> {productId};

  if (shopId) {
    routeParams.shopId = shopId;
  }

  return $.ajax({
    url: router.generate('admin_products_combinations_generate', routeParams),
    data,
    method: 'POST',
  });
};

export const bulkUpdate = async (
  productId: number,
  combinationIds: number[],
  formData: FormData,
  abortSignal: AbortSignal,
): Promise<Response> => {
  formData.append('_method', 'PATCH');
  formData.append('combinationIds', JSON.stringify(combinationIds));

  return fetch(router.generate('admin_products_combinations_bulk_edit_combination',
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
};

export default {
  deleteCombination,
  bulkDeleteCombinations,
  updateCombinationList,
  generateCombinations,
  bulkUpdate,
};
