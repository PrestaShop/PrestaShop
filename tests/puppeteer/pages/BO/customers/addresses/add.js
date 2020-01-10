require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddAddress extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Addresses •';
    this.pageTitleEdit = 'Edit •';

    // Selectors
    this.customerEmailInput = '#customer_address_customer_email';
    this.customerAddressdniInput = '#customer_address_dni';
    this.customerAddressAliaInput = '#customer_address_alias';
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
   * @param addressData
   * @param setEmailValue
   * @return {Promise<textContent>}
   */
  async createEditAddress(addressData, setEmailValue = true) {
    if (!setEmailValue) await this.setValue(this.customerEmailInput, addressData.email);
    await this.setValue(this.customerAddressdniInput, addressData.dni);
    await this.setValue(this.customerAddressAliaInput, addressData.alias);
    await this.setValue(this.customerAddressFirstNameInput, addressData.firstName);
    await this.setValue(this.customerLastNameInput, addressData.lastName);
    await this.setValue(this.customerAddressCompanyInput, addressData.company);
    await this.setValue(this.customerAddressVatNumberInput, addressData.vatNumber);
    await this.setValue(this.customerAddressInput, addressData.address);
    await this.setValue(this.customerSecondAddressInput, addressData.secondAddress);
    await this.setValue(this.customerAddressPostCodeInput, addressData.postalCode);
    await this.setValue(this.customerAddressCityInput, addressData.city);
    await this.selectByVisibleText(this.customerAddressCountrySelect, addressData.country);
    await this.setValue(this.customerAddressPhoneInput, addressData.phone);
    await this.setValue(this.customerAddressOtherInput, addressData.other);
    // Save address
    await this.clickAndWaitForNavigation(this.saveAddressButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
