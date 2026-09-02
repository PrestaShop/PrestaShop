/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
