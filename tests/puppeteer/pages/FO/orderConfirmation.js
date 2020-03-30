require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class OrderConfirmation extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order confirmation';
    this.orderConfirmationCardTitle = 'Your order is confirmed';

    // Selectors
    this.orderConfirmationCardSection = '#content-hook_order_confirmation';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h3.card-title`;
    this.orderSummaryContent = '#order-summary-content';
    this.conditionToApproveCheckbox = '#conditions_to_approve\\[terms-and-conditions\\]';
  }

  /*
    Methods
     */
  /**
   * Check if final summary is visible
   * @returns {boolean}
   */
  isFinalSummaryVisible() {
    return this.elementVisible(this.orderSummaryContent, 2000);
  }

  /**
   * Check if checkbox of condition to approve is visible
   * @returns {boolean}
   */
  isConditionToApproveCheckboxVisible() {
    return this.elementVisible(this.conditionToApproveCheckbox, 1000);
  }
};
