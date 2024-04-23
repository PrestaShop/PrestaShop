// Import FO pages
import {OrderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';

/**
 * @class
 * @extends FOBasePage
 */
class OrderDetails extends OrderDetailsPage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super('hummingbird');

    // Add message form selectors
    this.boxMessagesBlock = 'div.customer__message__content';
    this.reorderLink = '.order__details a';
    this.invoiceLink = '#content div.order__details div.order__header__left a[href*=\'pdf-invoice\']';
  }
}

export default new OrderDetails();
