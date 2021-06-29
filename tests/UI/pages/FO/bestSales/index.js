require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Best sales page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class BestSales extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on best sales page
   */
  constructor() {
    super();

    this.pageTitle = 'Best sales';
  }
}

module.exports = new BestSales();
