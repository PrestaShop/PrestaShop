require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Zones extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Zones •';

    // Selectors
    // SubTab
    this.countriesSubTab = '#subtab-AdminCountries';
  }

  /*
  Methods
   */
  /**
   * Go to sub tab countries
   * @param page
   * @returns {Promise<void>}
   */
  async goToSubTabCountries(page) {
    await this.clickAndWaitForNavigation(page, this.countriesSubTab);
  }
}
module.exports = new Zones();
