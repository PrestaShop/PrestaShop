require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View shopping page, contains functions that can be used on view shopping cart page
 * @class
 * @extends BOBasePage
 */
class ViewShoppingCarts extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view shopping cart page
   */
  constructor() {
    super();

    this.pageTitle = 'View';
  }

  /*
  Methods
   */
}

module.exports = new ViewShoppingCarts();
