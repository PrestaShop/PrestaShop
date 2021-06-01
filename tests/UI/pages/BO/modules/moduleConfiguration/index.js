require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Module configuration page, contains selectors and functions for the page.
 * Can be used as a base page for specific module configuration page.
 * @class
 * @extends BOBasePage
 */
class ModuleConfiguration extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on module configuration page
   */
  constructor() {
    super();

    // Header selectors
    this.pageHeadSubtitle = '.page-subtitle';
  }

  /* Methods */

  /**
   * Get module name from page title
   * @return {Promise<string>}
   */
  getPageSubtitle(page) {
    return this.getTextContent(page, this.pageHeadSubtitle);
  }
}

module.exports = new ModuleConfiguration();
