/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderDiscountsRefresher {
  router: Router;

  constructor() {
    this.router = new Router();
  }

  refresh(orderId: number): void {
    $.ajax(this.router.generate('admin_orders_get_discounts', {orderId}))
      .then((response) => {
        $(OrderViewPageMap.productDiscountList.list).replaceWith(response);
      });
  }
}
