require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Addresses page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Addresses extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on addresses page
   */
  constructor() {
    super();

    this.pageTitle = 'Addresses';
    this.addressPageTitle = 'Address';
    this.addAddressSuccessfulMessage = 'Address successfully added.';
    this.updateAddressSuccessfulMessage = 'Address successfully updated.';
    this.deleteAddressSuccessfulMessage = 'Address successfully deleted.';
    this.deleteAddressErrorMessage = 'Could not delete the address since it is used in the shopping cart.';

    // Selectors
    this.addressBlock = 'article.address';
    this.addressBodyTitle = `${this.addressBlock} .address-body h4`;
    this.createNewAddressLink = '#content div.addresses-footer a[data-link-action=\'add-address\']';
    this.editAddressLink = 'a[data-link-action=\'edit-address\']';
    this.deleteAddressLink = 'a[data-link-action=\'delete-address\']';
  }

  /*
  Methods
   */
  /**
   * Open create new address form
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   * @constructor
   */
  async openNewAddressForm(page) {
    if (await this.elementVisible(page, this.createNewAddressLink, 2000)) {
      await this.clickAndWaitForNavigation(page, this.createNewAddressLink);
    }
  }

  /**
   * Get address position from its alias
   * @param page {Page} Browser tab
   * @param alias {string} Alias of the address
   * @return {Promise<number>}
   */
  async getAddressPosition(page, alias) {
    const titles = await page.$$eval(
      this.addressBodyTitle,
      all => all.map(address => address.textContent),
    );

    return titles.indexOf(alias) + 1;
  }

  /**
   * Go to edit address page in FO
   * @param page {Page} Browser tab
   * @param position {string} String of the position
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
   * @param page {Page} Browser tab
   * @param position {string} String of the position
   * @returns {Promise<string>}
   */
  async deleteAddress(page, position = 'last') {
    const deleteButtons = await page.$$(this.deleteAddressLink);

    await Promise.all([
      page.waitForNavigation('networkidle'),
      deleteButtons[position === 'last' ? (deleteButtons.length - 1) : (position - 1)].click(),
    ]);

    return this.getTextContent(page, this.notificationsBlock);
  }
}

module.exports = new Addresses();
