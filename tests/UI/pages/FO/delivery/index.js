require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Delivery page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Delivery extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on delivery page
   */
  constructor() {
    super();

    this.pageTitle = 'Delivery';
  }
}

module.exports = new Delivery();
