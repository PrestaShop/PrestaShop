require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Prices drop page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class PricesDrop extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on prices drop page
   */
  constructor() {
    super();

    this.pageTitle = 'Prices drop';
  }
}

module.exports = new PricesDrop();
