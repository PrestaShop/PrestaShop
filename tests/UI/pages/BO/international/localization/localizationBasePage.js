require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Localization base page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
module.exports = class LocalizationBasePage extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on localization page
   */
  constructor() {
    super();

    this.localizationNavItemLink = '#subtab-AdminLocalization';
    this.languagesNavItemLink = '#subtab-AdminLanguages';
    this.currenciesNavItemLink = '#subtab-AdminCurrencies';
  }

  /* Header Methods */
  /**
   * Go to languages page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabLanguages(page) {
    await this.clickAndWaitForNavigation(page, this.languagesNavItemLink);
  }

  /**
   * Go to currencies page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies(page) {
    await this.clickAndWaitForNavigation(page, this.currenciesNavItemLink);
  }
};
