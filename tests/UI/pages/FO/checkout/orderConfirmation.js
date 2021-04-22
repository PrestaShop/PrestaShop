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
    this.orderReferenceValue = '#order-reference-value';
    this.customerSupportLink = '#content-hook_payment_return a';
  }

  /*
    Methods
     */
  /**
   * Check if final summary is visible
   * @param page {Page}
   *
   * @returns {boolean}
   */
  isFinalSummaryVisible(page) {
    return this.elementVisible(page, this.orderSummaryContent, 2000);
  }

  /**
   * Get order confirmation card title
   * @param page {Page}
   *
   * @return {Promise<string>}
   */
  getOrderConfirmationCardTitle(page) {
    return this.getTextContent(page, this.orderConfirmationCardTitleH3);
  }

  /**
   * Get and return the order reference value
   * @param page {Page}
   *
   * @returns {Promise<string>|Promise<TextContent>|*}
   */
  getOrderReferenceValue(page) {
    return this.getTextContent(page, this.orderReferenceValue);
  }

  /**
   * Click on the 'customer support' link
   * @param page {Page}
   *
   * @returns {Promise<void>}
   */
  async goToContactUsPage(page) {
    await this.clickAndWaitForNavigation(page, this.customerSupportLink);
  }
}

module.exports = new OrderConfirmation();
