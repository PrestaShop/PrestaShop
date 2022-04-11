require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Theme & logo page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ThemeAndLogo extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();

    this.pageTitle = 'Theme & Logo > Theme •';
  }
}

module.exports = new ThemeAndLogo();
