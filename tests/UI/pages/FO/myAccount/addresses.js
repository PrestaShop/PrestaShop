require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Addresses extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Addresses';
    this.addAddressSuccessfulMessage = 'Address successfully added!';
    this.updateAddressSuccessfulMessage = 'Address successfully updated!';
    this.deleteAddressSuccessfulMessage = 'Address successfully deleted!';

    // Selectors
    this.createNewAddressLink = '#content div.addresses-footer a[data-link-action=\'add-address\']';
    this.editAddressLink = 'a[data-link-action=\'edit-address\']';
    this.deleteAddressLink = 'a[data-link-action=\'delete-address\']';
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
    await this.clickAndWaitForNavigation(page, this.createNewAddressLink);
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

  /**
   * Go to edit address page in FO
   * @param page
   * @param position
   * @returns {Promise<void>}
   */
  async goToEditAddressPage(page, position = 'last') {
    const editButtons = await page.$$(this.editAddressLink);

    await Promise.all([
      page.waitForNavigation('networkidle'),
      editButtons[position === 'last' ? (editButtons.length - 1) : (position - 1)].click(),
    ]);
  }

  /**
   * Delete address in FO
   * @param page
   * @param position
   * @returns {Promise<string>}
   */
  async deleteAddress(page, position = 'last') {
    const deleteButtons = await page.$$(this.deleteAddressLink);

    await Promise.all([
      page.waitForNavigation('networkidle'),
      deleteButtons[position === 'last' ? (deleteButtons.length - 1) : (position - 1)].click(),
    ]);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new Addresses();
