require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class LocalizationBasePage extends BOBasePage {
  constructor(page) {
    super(page);

    this.localizationNavItemLink = '#subtab-AdminLocalization';
    this.languagesNavItemLink = '#subtab-AdminLanguages';
    this.currenciesNavItemLink = '#subtab-AdminCurrencies';
    this.geolocationNavItemLink = '#subtab-AdminGeolocation';
  }

  /* Header Methods */
  /**
   * Go to localization page
   * @return {Promise<void>}
   */
  async goToSubTabLocalization() {
    await this.clickAndWaitForNavigation(this.localizationNavItemLink);
  }

  /**
   * Go to languages page
   * @return {Promise<void>}
   */
  async goToSubTabLanguages() {
    await this.clickAndWaitForNavigation(this.languagesNavItemLink);
  }

  /**
   * Go to currencies page
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies() {
    await this.clickAndWaitForNavigation(this.currenciesNavItemLink);
  }

  /**
   * Go to geolocation page
   * @return {Promise<void>}
   */
  async goToSubTabGeolocation() {
    await this.clickAndWaitForNavigation(this.geolocationNavItemLink);
  }
};
