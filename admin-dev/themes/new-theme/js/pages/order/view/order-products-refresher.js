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
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderProductsRefresher {
  constructor() {
    this.router = new Router();
  }

  refresh(orderId) {
    $.ajax(this.router.generate('admin_orders_get_products', {orderId})).then((response) => {
      const $orderProducts = response.products;
      const $orderDetailIds = $orderProducts.map(product => product.orderDetailId);

      // Remove products that don't belong to the order anymore
      let orderDetailRows = document.querySelectorAll('tr.cellProduct');
      orderDetailRows.forEach(orderDetailRow => {
        const $orderDetailRowId = parseInt(($(orderDetailRow).attr('id').match(/\d+/g))[0], 10);

        if (! $orderDetailIds.includes($orderDetailRowId)) {
          this.removeProductRow($orderDetailRowId);
        }
      });

      // Add products that are not displayed
      // Page needs to be refreshed ?
    });
  }

  removeProductRow(orderDetailRowId) {
    // Remove the row
    const $row = $(OrderViewPageMap.productsTableRow(orderDetailRowId));
    const $next = $row.next();
    $row.remove();
    if ($next.hasClass('order-product-customization')) {
      $next.remove();
    }
  }
}
