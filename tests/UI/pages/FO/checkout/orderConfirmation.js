require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Order confirmation page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmation extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on order confirmation page
   */
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

  /**
   * Get and return the order reference value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderReferenceValue(page) {
    const orderRefText = await this.getTextContent(page, this.orderReferenceValue);
    return (orderRefText.split(':'))[1].trim();
  }

  /**
   * Click on the 'customer support' link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToContactUsPage(page) {
    await this.clickAndWaitForNavigation(page, this.customerSupportLink);
  }
}

module.exports = new OrderConfirmation();
