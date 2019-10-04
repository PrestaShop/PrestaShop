/**
 * 2007-2019 PrestaShop SA and Contributors
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

import createOrderPageMap from './create-order-map';

const $ = window.$;

/**
 * Provides ajax calls for getting cart information
 */
export default class CartProvider {
  constructor() {
    this.$container = $(createOrderPageMap.orderCreationContainer);
  }

  /**
   * Gets cart information
   *
   * @param cartId
   *
   * @returns {jqXHR}. Object with cart information in response.
   */
  getCart(cartId) {
    return $.ajax(this.$container.data('get-cart-info-url'), {
      method: 'GET',
      data: {
        cartId,
      },
      dataType: 'json',
    });
  }

  /**
   * Gets existing empty cart or creates new empty cart for customer.
   *
   * @param customerId
   *
   * @returns {jqXHR}. Object with cart information in response
   */
  loadEmptyCart(customerId) {
    return $.ajax(this.$container.data('create-empty-cart-url'), {
      method: 'POST',
      data: {
        customerId,
      },
      dataType: 'json',
    });
  }

  /**
   * Duplicates cart from provided order
   *
   * @param orderId
   *
   * @returns {jqXHR}. Object with cart information in response
   */
  duplicateOrderCart(orderId) {
    return $.ajax(this.$container.data('duplicate-order-cart-url'), {
      method: 'POST',
      data: {
        orderId,
      },
      dataType: 'json',
    });
  }
}
