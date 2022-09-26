require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Credit slip page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CreditSlip extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on credit slip page
   */
  constructor() {
    super();

    // Title
    this.pageTitle = 'Credit slip';

    // Message

    this.noCreditSlipsInfoMessage = 'You have not received any credit slips.';
    // Alert block selectors
    this.alertInfoBlock = '#content .alert.alert-info';
  }

  /**
   * Get alert info message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertInfoMessage(page) {
    return this.getTextContent(page, this.alertInfoBlock);
  }
}

module.exports = new CreditSlip();
