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

    this.pageTitle = 'Credit slip';
  }
}

module.exports = new CreditSlip();
