require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Module catalog page, contains selectors and functions for the page, Page should not exist for V8 and above
 * @class
 * @extends BOBasePage
 */
class ModuleCatalog extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on module catalog page
   */
  constructor() {
    super();

    // Selectors
    this.alertDangerBlockParagraph = '.alert-danger';
  }
}

module.exports = new ModuleCatalog();
