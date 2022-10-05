require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Return details page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class ReturnDetails extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on return details page
   */
  constructor() {
    super();

    this.pageTitle = 'Return Details';
    this.errorMessage = 'You must wait for confirmation before returning any merchandise.';
    this.orderReturnCardBlock = 'We have logged your return request. Your package must be returned to us within 14 days'
      + ' of receiving your order. The current status of your merchandise return is:';

    // Selectors
    this.pageTitleHeader = '#main header h1.h1';
    this.alertWarning = '#notifications .notifications-container article.alert-warning';
    this.orderReturnInfo = '#order-return-infos';
  }

  /*
  Methods
   */

  /**
   * Get page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageTitle(page) {
    return this.getTextContent(page, this.pageTitleHeader);
  }

  /**
   * get return notifications
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getAlertWarning(page) {
    return this.getTextContent(page, this.alertWarning);
  }

  /**
   * Is alert warning visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAlertWarningVisible(page) {
    return this.elementVisible(page, this.alertWarning);
  }

  /**
   * Get order return info
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getOrderReturnInfo(page) {
    return this.getTextContent(page, this.orderReturnInfo);
  }
}

module.exports = new ReturnDetails();
