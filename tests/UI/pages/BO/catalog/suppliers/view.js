require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewSupplier extends BOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.contentDiv = 'div.content-div';
    this.productsGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(2)`;
    this.productsGridHeader = `${this.productsGrid} h3.card-header`;
  }

  /*
  Methods
   */
};
