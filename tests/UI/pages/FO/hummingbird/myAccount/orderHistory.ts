// Import FO pages
import {OrderHistoryPage} from '@pages/FO/myAccount/orderHistory';

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
  }
}

export default new OrderHistory();
