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
 * Renders customer orders list
 */
export default class OrdersRenderer {
  /**
   * Renders customer orders
   *
   * @param {Array} orders
   */
  render(orders) {
    const $ordersTable = $(createOrderPageMap.customerOrdersTable);
    const $rowTemplate = $($(createOrderPageMap.customerOrdersTableRowTemplate).html());

    $ordersTable.find('tbody').empty();

    if (!orders) {
      return;
    }

    this._showCheckoutHistoryBlock();

    for (const key in Object.keys(orders)) {
      const order = orders[key];
      const $template = $rowTemplate.clone();

      $template.find('.js-order-id').text(order.orderId);
      $template.find('.js-order-date').text(order.orderPlacedDate);
      $template.find('.js-order-products').text(order.totalProductsCount);
      $template.find('.js-order-total-paid').text(order.totalPaid);
      $template.find('.js-order-status').text(order.orderStatus);

      $template.find('.js-use-order-btn').data('order-id', order.orderId);

      $ordersTable.find('tbody').append($template);
    }
  }

  /**
   * Shows checkout history block where carts and orders are rendered
   *
   * @private
   */
  _showCheckoutHistoryBlock() {
    $(createOrderPageMap.customerCheckoutHistory).removeClass('d-none');
  }
}
