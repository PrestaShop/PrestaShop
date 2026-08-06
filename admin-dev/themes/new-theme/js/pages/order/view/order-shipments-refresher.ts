/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

export default class OrderShipmentsRefresher {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  refresh(orderId: number): void {
    fetch(this.router.generate('admin_orders_get_shipments', {orderId}))
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Unable to retrieve shipments for order ${orderId}`);
        }
        return response.json();
      })
      .then((response) => {
        const countElement = document.querySelector(OrderViewPageMap.orderShipmentsTabCount);

        if (countElement) {
          countElement.textContent = response.total;
        }
        const bodyElement = document.querySelector(OrderViewPageMap.orderShipmentsTabBody);

        if (bodyElement) {
          bodyElement.innerHTML = response.html;
        }
      });
  }
}
