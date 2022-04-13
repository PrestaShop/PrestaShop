require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Theme & Logo base page, contains functions that can be used on the page
 * @class
 * @type {themeAndLogoBasePage}
 */
module.exports = class themeAndLogoBasePage extends BOBasePage {

  /**
   * @constructs
   * Setting up texts and selectors to use on theme & logo page
   */
  constructor() {
    super();
    this.advancedCustomizationNavItemLink = '#subtab-AdminPsThemeCustoAdvanced';
  }

  /* Header Methods */
  /**
   * Go to advanced customizationpage
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabAdvancedCustomization(page) {
    await this.clickAndWaitForNavigation(page, this.advancedCustomizationNavItemLink);
  }
};
