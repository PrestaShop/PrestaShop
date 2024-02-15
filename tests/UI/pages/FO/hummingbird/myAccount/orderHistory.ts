// Import FO pages
import {OrderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';

/**
 * @class
 * @extends FOBasePage
 */
class OrderHistory extends OrderHistoryPage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.detailsLink = (row: number) => `${this.ordersTableRow(row)} a[data-link-action="view-order-details"]`;
  }
}

export default new OrderHistory();
