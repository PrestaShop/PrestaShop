// Import FO pages
import {OrderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmation extends OrderConfirmationPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.orderConfirmationCardSection = '#content-wrapper .alert';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h1.alert-heading`;
    this.orderDetailsTable = 'div.order-confirmation__details ul.order-details';
    this.orderReferenceValue = `${this.orderDetailsTable} li:nth-child(1)`;
    this.customerSupportLink = 'div.card .card-footer a.alert-link';
    this.paymentMethodRow = `${this.orderDetailsTable} li:nth-child(2)`;
  }
}

export default new OrderConfirmation();
