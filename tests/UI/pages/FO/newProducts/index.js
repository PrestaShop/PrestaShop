import FOBasePage from '@pages/FO/FObasePage';

require('module-alias/register');

/**
 * New products page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class NewProducts extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on new products page
   */
  constructor() {
    super();

    this.pageTitle = 'New products';
  }
}

module.exports = new NewProducts();
