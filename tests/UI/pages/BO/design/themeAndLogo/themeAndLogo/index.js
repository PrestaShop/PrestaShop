require('module-alias/register');
const themeAndLogoBasePage = require('@pages/BO/design/themeAndLogo/themeAndLogo/themeAndLogoBasePage');

/**
 * Theme & logo page, contains functions that can be used on the page
 * @class
 * @extends themeAndLogoBasePage
 */
class ThemeAndLogo extends themeAndLogoBasePage {
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
