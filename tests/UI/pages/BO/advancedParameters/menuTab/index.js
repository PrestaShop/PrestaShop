require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * MenuTab page, should not be displayed on BO
 * @class
 * @extends BOBasePage
 */
class MenuTab extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on MenuTab page
   */
  constructor() {
    super();

    this.pageTitle = 'Menus';

    // Selectors
    this.alertDangerBlockParagraph = '.alert-danger';
    this.pageH1Title = 'h1.page-title';
  }

  // Functions

  /**
   * @override
   * Get title from selector instead of header
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPageTitle(page) {
    return this.getTextContent(page, this.pageH1Title);
  }
}

module.exports = new MenuTab();
