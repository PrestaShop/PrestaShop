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
 * Provides ajax calls for getting customer information
 */
export default class CustomerInfoProvider {
  constructor() {
    this.$container = $(createOrderPageMap.orderCreationContainer);
  }

  /**
   * Gets customer carts
   *
   * @param customerId
   *
   * @returns {jqXHR}. Array of carts in response.
   */
  getCustomerCarts(customerId) {
    return $.ajax(this.$container.data('customer-carts-url'), {
      method: 'GET',
      data: {
        customerId,
      },
      dataType: 'json',
    });
  }

  /**
   * Gets customer orders
   *
   * @param customerId
   *
   * @returns {jqXHR}. Array of orders in response.
   */
  getCustomerOrders(customerId) {
    return $.ajax(this.$container.data('customer-orders-url'), {
      method: 'GET',
      data: {
        customerId,
      },
      dataType: 'json',
    });
  }
}
