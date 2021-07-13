require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Add address page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AddAddress extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add address page
   */
  constructor() {
    super();

    this.pageTitle = 'Address';
    this.creationFormTitle = 'New address';
    this.updateFormTitle = 'Update your address';

    // Selectors
    this.pageHeaderTitle = '#main .page-header h1';
    this.addressForm = '.address-form';
    this.aliasInput = `${this.addressForm} input[name=alias]`;
    this.firstnameInput = `${this.addressForm} input[name=firstname]`;
    this.lastnameInput = `${this.addressForm} input[name=lastname]`;
    this.companyInput = `${this.addressForm} input[name=company]`;
    this.vatNumberInput = `${this.addressForm} input[name=vat_number]`;
    this.addressInput = `${this.addressForm} input[name=address1]`;
    this.secondAddressInput = `${this.addressForm} input[name=address2]`;
    this.postCodeInput = `${this.addressForm} input[name=postcode]`;
    this.cityInput = `${this.addressForm} input[name=city]`;
    this.countrySelect = `${this.addressForm} select[name=id_country]`;
    this.phoneInput = `${this.addressForm} input[name=phone]`;
    this.saveButton = `${this.addressForm} input[name=submitAddress] + button`;
  }

  /*
  Methods
   */

  /**
   * Get form header title
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getHeaderTitle(page) {
    return this.getTextContent(page, this.pageHeaderTitle);
  }

  /**
   * Fill address form and save
   * @param page {Page} Browser tab
   * @param addressData {object} Address's information to fill on form
   * @returns {Promise<string>}
   */
  async setAddress(page, addressData) {
    // Set alias if added (optional)
    if (addressData.alias) {
      await this.setValue(page, this.aliasInput, addressData.alias);
    }

    await this.setValue(page, this.firstnameInput, addressData.firstName);
    await this.setValue(page, this.lastnameInput, addressData.lastName);

    // Set company if added (optional)
    if (addressData.company) {
      await this.setValue(page, this.companyInput, addressData.company);
    }

    // Set vat number if added (optional)
    if (addressData.vatNumber) {
      await this.setValue(page, this.vatNumberInput, addressData.vatNumber);
    }

    await this.setValue(page, this.addressInput, addressData.address);

    // Set second address if added (optional)
    if (addressData.secondAddress) {
      await this.setValue(page, this.secondAddressInput, addressData.secondAddress);
    }

    await this.setValue(page, this.postCodeInput, addressData.postalCode);
    await this.setValue(page, this.cityInput, addressData.city);
    await this.selectByVisibleText(page, this.countrySelect, addressData.country);

    // Set phone if added (optional)
    if (addressData.phone) {
      await this.setValue(page, this.phoneInput, addressData.phone);
    }

    // Save address
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Is vat number input is required
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isVatNumberRequired(page) {
    return this.elementVisible(page, `${this.vatNumberInput}:required`, 1000);
  }
}

module.exports = new AddAddress();
