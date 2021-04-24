require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class LocalizationBasePage extends BOBasePage {
  constructor() {
    super();

    this.localizationNavItemLink = '#subtab-AdminLocalization';
    this.languagesNavItemLink = '#subtab-AdminLanguages';
    this.currenciesNavItemLink = '#subtab-AdminCurrencies';
  }

  /* Header Methods */
  /**
   * Go to languages page
   * @param page
   * @return {Promise<void>}
   */
  async goToSubTabLanguages(page) {
    await this.clickAndWaitForNavigation(page, this.languagesNavItemLink);
  }

  /**
   * Go to currencies page
   * @param page
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies(page) {
    await this.clickAndWaitForNavigation(page, this.currenciesNavItemLink);
  }
};
