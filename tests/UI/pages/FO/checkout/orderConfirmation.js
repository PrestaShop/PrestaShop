require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class OrderConfirmation extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Order confirmation';
    this.orderConfirmationCardTitle = 'Your order is confirmed';

    // Selectors
    this.orderConfirmationCardSection = '#content-hook_order_confirmation';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h3.card-title`;
    this.orderSummaryContent = '#order-summary-content';
  }

  /*
    Methods
     */
  /**
   * Check if final summary is visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isFinalSummaryVisible(page) {
    return this.elementVisible(page, this.orderSummaryContent, 2000);
  }

  /**
   * Get order confirmation card title
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getOrderConfirmationCardTitle(page) {
    return this.getTextContent(page, this.orderConfirmationCardTitleH3);
  }
}

module.exports = new OrderConfirmation();
