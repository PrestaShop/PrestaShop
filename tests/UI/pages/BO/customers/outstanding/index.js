require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Outstanding page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Outstanding extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on 03_outstanding page
   */
  constructor() {
    super();

    this.pageTitle = 'Outstanding • PrestaShop';
  }
}

module.exports = new Outstanding();
