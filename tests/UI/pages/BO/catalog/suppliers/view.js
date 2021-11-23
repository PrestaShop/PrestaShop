require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View supplier page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewSupplier extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view supplier page
   */
  constructor() {
    super();

    // Selectors
    this.contentDiv = 'div.content-div';
    this.productsGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(2)`;
    this.productsGridHeader = `${this.productsGrid} h3.card-header`;
  }

  /*
  Methods
   */
}

module.exports = new ViewSupplier();
