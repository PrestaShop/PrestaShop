/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderShippingRefresher {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  refresh(orderId: number): void {
    $.getJSON(this.router.generate('admin_orders_get_shipping', {orderId}))
      .then((response) => {
        $(OrderViewPageMap.orderShippingTabCount).text(response.total);
        $(OrderViewPageMap.orderShippingTabBody).html(response.html);
      });
  }
}
