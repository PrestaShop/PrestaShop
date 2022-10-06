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

    this.pageTitle = 'View • PrestaShop';

    // Selectors
    this.threadBadge = '#main-div div[data-role="messages-thread"] .card-header strong';
    this.messagesThredDiv = '#main-div div[data-role="messages-thread"]';
    this.attachmentLink = `${this.messagesThredDiv} a[href*='/upload']`;
    this.statusButton = statusName => `${this.messagesThredDiv} form[action*='/update-status/${statusName}'] button`;
    this.yourAnswerFormTitle = '#main-div div[data-role="employee-answer"] h3.card-header';
    this.yourAnswerFormTextarea = '#main-div div[data-role="employee-answer"]';
    this.ordersAndMessagesBlock = '#main-div div[data-role="messages_timeline"]';
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
    return this.getTextContent(page, this.messagesThredDiv);
  }

  /**
   * Get attached href
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAttachedFileHref(page) {
    return this.getAttributeContent(page, this.attachmentLink, 'href');
  }

  // Your answer form
  /**
   * Get your answer form title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getYourAnswerFormTitle(page) {
    return this.getTextContent(page, this.yourAnswerFormTitle);
  }

  /**
   * Get your answer form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getYourAnswerFormContent(page) {
    return this.getTextContent(page, this.yourAnswerFormTextarea);
  }

  // Orders and messages timeline form
  /**
   * Get orders and messages form content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getOrdersAndMessagesTimeline(page) {
    return this.getTextContent(page, this.ordersAndMessagesBlock);
  }

  /**
   * Set status
   * @param page {Page} Browser tab
   * @param status {string} Status to set on the message
   * @returns {Promise<string>}
   */
  async setStatus(page, status) {
    let statusName;
    switch (status) {
      case 'Re-open':
        statusName = 'open';
        break;

      case 'Handled':
        statusName = 'closed';
        break;

      case 'Pending 1':
        statusName = 'pending1';
        break;

      case 'Pending 2':
        statusName = 'pending2';
        break;

      default:
        throw new Error(`Status ${status} was not found`);
    }

    await this.waitForSelectorAndClick(page, this.statusButton(statusName));

    if (status === 'Re-open') {
      return this.getTextContent(page, this.statusButton('closed'));
    }
    return this.getTextContent(page, this.statusButton('open'));
  }
}

module.exports = new ViewCustomer();
