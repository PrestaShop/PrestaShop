require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Addresses extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Addresses';

    // Selectors
    this.createNewAddressLink = '#content div.addresses-footer a[data-link-action=\'add-address\']';
    this.countrySelect = '#content  select[name=\'id_country\']';
  }

  /*
  Methods
   */
  /**
   * Open create new address form
   * @param page
   * @returns {Promise<void>}
   * @constructor
   */
  async openNewAddressForm(page) {
    await this.waitForSelectorAndClick(page, this.createNewAddressLink);
  }

  /**
   * Is country exist
   * @param page
   * @param countryName
   * @returns {Promise<boolean>}
   */
  async isCountryExist(page, countryName) {
    let options = await page.$$eval(
      `${this.countrySelect} option`,
      all => all.map(
        option => ({
          textContent: option.textContent,
          value: option.value,
        })),
    );
    options = await options.filter(option => countryName === option.textContent);
    return options.length !== 0;
  }
}

module.exports = new Addresses();
