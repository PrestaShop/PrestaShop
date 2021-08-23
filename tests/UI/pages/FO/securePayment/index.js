require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Secure payment page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SecurePayment extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on secure payment page
   */
  constructor() {
    super();

    this.pageTitle = 'Secure payment';
  }
}

module.exports = new SecurePayment();
