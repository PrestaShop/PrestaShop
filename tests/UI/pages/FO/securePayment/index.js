import FOBasePage from '@pages/FO/FObasePage';

require('module-alias/register');

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
