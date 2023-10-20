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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import Router from '@components/router';
import {EventEmitter} from '@components/event-emitter';
import OrderViewEventMap from '@pages/order/view/order-view-event-map';

const {$} = window;

export default class OrderProductManager {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  handleDeleteProductEvent(event: JQueryEventObject): void {
    event.preventDefault();

    const $btn = $(event.currentTarget);
    const confirmed = window.confirm($btn.data('deleteMessage'));

    if (!confirmed) {
      return;
    }

    $btn.pstooltip('dispose');
    $btn.prop('disabled', true);
    this.deleteProduct($btn.data('orderId'), $btn.data('orderDetailId'));
  }

  deleteProduct(orderId: number, orderDetailId: number): void {
    $.ajax(this.router.generate('admin_orders_delete_product', {orderId, orderDetailId}), {
      method: 'POST',
    }).then(() => {
      EventEmitter.emit(OrderViewEventMap.productDeletedFromOrder, {
        oldOrderDetailId: orderDetailId,
        orderId,
      });
    }, (response: Record<string, any>) => {
      if (response.responseJSON && response.responseJSON.message) {
        $.growl.error({message: response.responseJSON.message});
      }
    });
  }
}
