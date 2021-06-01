require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View customer service page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ViewCustomer extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on view customer service page
   */
  constructor() {
    super();

    this.pageTitle = 'Customer Service > View •';

    // Selectors
    this.threadBadge = 'span.badge';
    this.statusButton = id => `button[name='setstatus'][value='${id}']`;
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
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getBadgeNumber(page) {
    return this.getTextContent(page, this.threadBadge);
  }

  /**
   * Get customer message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerMessage(page) {
    return this.getTextContent(page, this.messageDiv);
  }

  // Your answer form
  /**
   * Get your answer form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getYourAnswerContent(page) {
    return this.getTextContent(page, this.yourAnswerForm);
  }

  // Orders and messages timeline form
  /**
   * Get orders and messages form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getOrdersAndMessagesTimeline(page) {
    return this.getTextContent(page, this.ordersAndMessagesForm);
  }

  /**
   * Set status
   * @param page {Page} Browser tab
   * @param status {string} Status to set on the message
   * @returns {Promise<string>}
   */
  async setStatus(page, status) {
    let statusID = 0;
    switch (status) {
      case 'Re-open':
        statusID = 1;
        break;

      case 'Handled':
        statusID = 2;
        break;

      case 'Pending 1':
        statusID = 3;
        break;

      case 'Pending 2':
        statusID = 4;
        break;

      default:
        throw new Error(`Status ${status} was not found`);
    }

    await this.waitForSelectorAndClick(page, this.statusButton(statusID));

    if (status === 'Re-open') {
      return this.getTextContent(page, this.statusButton(2));
    }
    return this.getTextContent(page, this.statusButton(1));
  }
}

module.exports = new ViewCustomer();
