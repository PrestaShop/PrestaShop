/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import createOrderPageMap from '@pages/order/create/create-order-map';
import Router from '@components/router';
import {EventEmitter} from '@components/event-emitter';
import eventMap from '@pages/order/create/event-map';

const {$} = window;

/**
 * Provides ajax calls for getting cart information
 */
export default class CartProvider {
  $container: JQuery;

  router: Router;

  constructor() {
    this.$container = $(createOrderPageMap.orderCreationContainer);
    this.router = new Router();
  }

  /**
   * Gets cart information
   *
   * @param cartId
   */
  getCart(cartId: number): void {
    $.get(this.router.generate('admin_carts_info', {cartId})).then(
      (cartInfo) => {
        EventEmitter.emit(eventMap.cartLoaded, cartInfo);
      },
    );
  }

  /**
   * Gets existing empty cart or creates new empty cart for customer.
   *
   * @param customerId
   */
  loadEmptyCart(customerId: number): void {
    $.post(this.router.generate('admin_carts_create'), {
      customerId,
    }).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }

  /**
   * Duplicates cart from provided order
   *
   * @param orderId
   */
  duplicateOrderCart(orderId: number): void {
    $.post(
      this.router.generate('admin_orders_duplicate_cart', {orderId}),
    ).then((cartInfo) => {
      EventEmitter.emit(eventMap.cartLoaded, cartInfo);
    });
  }
}
