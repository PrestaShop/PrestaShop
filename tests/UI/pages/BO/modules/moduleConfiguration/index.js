require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ModuleConfiguration extends BOBasePage {
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
