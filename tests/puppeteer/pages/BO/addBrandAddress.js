require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddBrandAddress extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Add new address • ';

    // Selectors
    this.brandSelect = 'select#manufacturer_address_id_manufacturer';
    this.lastnameInput = 'input#manufacturer_address_last_name';
    this.firstnameInput = 'input#manufacturer_address_first_name';
    this.addressInput = 'input#manufacturer_address_address';
    this.secondaryAddressInput = 'input#manufacturer_address_address2';
    this.postalCodeInput = 'input#manufacturer_address_post_code';
    this.cityInput = 'input#manufacturer_address_city';
    this.countrySelect = 'select#manufacturer_address_id_country';
    this.homePhoneInput = 'input#manufacturer_address_home_phone';
    this.mobilePhoneInput = 'input#manufacturer_address_mobile_phone';
    this.otherInput = 'input#manufacturer_address_other';
    this.saveButton = '.card-footer button';
  }

  /*
  Methods
   */
  /**
   * Create or edit Brand Address
   * @param brandAddressData
   * @return {Promise<textContent>}
   */
  async createEditBrandAddress(brandAddressData) {
    // Fill information data
    await this.selectByVisibleText(this.brandSelect, brandAddressData.brandName);
    await this.setValue(this.lastnameInput, brandAddressData.lastName);
    await this.setValue(this.firstnameInput, brandAddressData.firstName);
    await this.setValue(this.addressInput, brandAddressData.address);
    await this.setValue(this.secondaryAddressInput, brandAddressData.secondaryAddress);
    await this.setValue(this.postalCodeInput, brandAddressData.postalCode);
    await this.setValue(this.cityInput, brandAddressData.city);
    await this.selectByVisibleText(this.countrySelect, brandAddressData.country);
    await this.setValue(this.homePhoneInput, brandAddressData.homePhone);
    await this.setValue(this.mobilePhoneInput, brandAddressData.mobilePhone);
    await this.setValue(this.otherInput, brandAddressData.other);
    // Click on Save button and successful message
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
