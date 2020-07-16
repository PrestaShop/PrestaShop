require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddAddress extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Addresses •';
    this.pageTitleEdit = 'Edit •';

    // Selectors
    this.customerEmailInput = '#customer_address_customer_email';
    this.customerAddressdniInput = '#customer_address_dni';
    this.customerAddressAliasInput = '#customer_address_alias';
    this.customerAddressFirstNameInput = '#customer_address_first_name';
    this.customerLastNameInput = '#customer_address_last_name';
    this.customerAddressCompanyInput = '#customer_address_company';
    this.customerAddressVatNumberInput = '#customer_address_vat_number';
    this.customerAddressInput = '#customer_address_address1';
    this.customerAddressPostCodeInput = '#customer_address_postcode';
    this.customerSecondAddressInput = '#customer_address_address2';
    this.customerAddressCityInput = '#customer_address_city';
    this.customerAddressCountrySelect = '#customer_address_id_country';
    this.customerAddressPhoneInput = '#customer_address_phone';
    this.customerAddressOtherInput = '#customer_address_other';
    this.saveAddressButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit address
   * @param page
   * @param addressData
   * @returns {Promise<string>}
   */
  async createEditAddress(page, addressData) {
    if (await this.elementVisible(page, this.customerEmailInput, 2000)) {
      await this.setValue(page, this.customerEmailInput, addressData.email);
    }
    await this.setValue(page, this.customerAddressdniInput, addressData.dni);
    await this.setValue(page, this.customerAddressAliasInput, addressData.alias);
    await this.setValue(page, this.customerAddressFirstNameInput, addressData.firstName);
    await this.setValue(page, this.customerLastNameInput, addressData.lastName);
    await this.setValue(page, this.customerAddressCompanyInput, addressData.company);
    await this.setValue(page, this.customerAddressVatNumberInput, addressData.vatNumber);
    await this.setValue(page, this.customerAddressInput, addressData.address);
    await this.setValue(page, this.customerSecondAddressInput, addressData.secondAddress);
    await this.setValue(page, this.customerAddressPostCodeInput, addressData.postalCode);
    await this.setValue(page, this.customerAddressCityInput, addressData.city);
    await this.selectByVisibleText(page, this.customerAddressCountrySelect, addressData.country);
    await this.setValue(page, this.customerAddressPhoneInput, addressData.phone);
    await this.setValue(page, this.customerAddressOtherInput, addressData.other);
    // Save address
    await this.clickAndWaitForNavigation(page, this.saveAddressButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new AddAddress();
