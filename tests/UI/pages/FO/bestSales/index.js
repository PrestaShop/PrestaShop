import FOBasePage from '@pages/FO/FObasePage';

require('module-alias/register');

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

    this.pageTitle = 'Best sellers';
  }
}

module.exports = new BestSales();
