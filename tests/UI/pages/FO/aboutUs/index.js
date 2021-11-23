require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * About us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AboutUs extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on about us page
   */
  constructor() {
    super();

    this.pageTitle = 'About us';
  }
}

module.exports = new AboutUs();
