require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewCustomer extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Customer Service > View •';

    // Selectors
    this.threadBadge = 'span.badge';
    this.messageDiv = '#content div.message-item-initial';
    this.yourAnswerForm = '#content div.row div:nth-child(5) form';
    this.ordersAndMessagesForm = '#content div.row div:nth-child(6)';
  }

  /*
  Methods
   */

  // Thread form
  /**
   * Get badge number
   * @param page
   * @returns {Promise<string>}
   */
  getBadgeNumber(page) {
    return this.getTextContent(page, this.threadBadge);
  }

  /**
   * Get customer message
   * @param page
   * @returns {Promise<string>}
   */
  getCustomerMessage(page) {
    return this.getTextContent(page, this.messageDiv);
  }

  // Your answer form
  getYourAnswerContent(page) {
    return this.getTextContent(page, this.yourAnswerForm);
  }

  // Orders and messages timeline form
  getOrdersAndMessagesTimeline(page) {
    return this.getTextContent(page, this.ordersAndMessagesForm);
  }
}

module.exports = new ViewCustomer();
