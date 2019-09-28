const CommonPage = require('../../pages/commonPage');

module.exports = class OrderConfirmation extends CommonPage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Order confirmation';
    this.orderConfirmationCardTitle = 'Your order is confirmed';

    // Selectors
    this.orderConfirmationCardSection = '#content-hook_order_confirmation';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h3.card-title`;
  }
};
