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

  async isCountryExist(page, countryName) {
    let found = false;
    let options = await page.$$eval(
      `${this.countrySelect} option`,
      all => all.map(
        option => ({
          textContent: option.textContent,
          value: option.value,
        })),
    );
    options = await options.filter(option => countryName === option.textContent);
    if (options.length !== 0) {
      const elementValue = await options[0].value;
      await page.selectOption(this.countrySelect, elementValue);
      found = true;
    }
    return found;
  }
}

module.exports = new Addresses();
