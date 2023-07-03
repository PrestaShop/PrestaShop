import BOBasePage from '@pages/BO/BObasePage';

import type AddressData from '@data/faker/address';

import type {Frame, Page} from 'playwright';

/**
 * Add address page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddAddress extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly customerEmailInput: string;

  private readonly customerAddressdniInput: string;

  private readonly customerAddressAliasInput: string;

  private readonly customerAddressFirstNameInput: string;

  private readonly customerLastNameInput: string;

  private readonly customerAddressCompanyInput: string;

  private readonly customerAddressVatNumberInput: string;

  private readonly customerAddressInput: string;

  private readonly customerAddressPostCodeInput: string;

  private readonly customerSecondAddressInput: string;

  private readonly customerAddressCityInput: string;

  private readonly customerAddressCountrySelect: string;

  private readonly customerAddressCountryOption: string;

  private readonly customerAddressStateSelect: string;

  private readonly searchStateInput: string;

  private readonly searchResultState: string;

  private readonly customerAddressPhoneInput: string;

  private readonly customerAddressOtherInput: string;

  private readonly saveAddressButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add address page
   */
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
    this.customerAddressCountryOption = `${this.customerAddressCountrySelect} option`;
    this.customerAddressStateSelect = '#select2-customer_address_id_state-container';
    this.searchStateInput = '.select2-search__field';
    this.searchResultState = '.select2-results__option.select2-results__option--highlighted';
    this.customerAddressPhoneInput = '#customer_address_phone';
    this.customerAddressOtherInput = '#customer_address_other';
    this.saveAddressButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit address
   * @param page {Frame|Page} Browser tab
   * @param addressData {AddressData} Data to set on new address form
   * @param save {boolean} True if we need to save the new address, false if not
   * @param waitForNavigation {boolean} True if we need to wait for navigation after save, false if not
   * @returns {Promise<?string>}
   */
  async createEditAddress(
    page: Frame|Page,
    addressData: AddressData,
    save: boolean = true,
    waitForNavigation: boolean = true,
  ): Promise<string|null> {
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

    if (await this.elementVisible(page, this.customerAddressStateSelect, 1000)) {
      await page.click(this.customerAddressStateSelect);
      await this.setValue(page, this.searchStateInput, addressData.state);
      await this.waitForSelectorAndClick(page, this.searchResultState);
    }

    // Save and return successful message
    if (save) {
      if (waitForNavigation) {
        return this.saveAddress(page);
      }

      await page.click(this.saveAddressButton);
    }

    return null;
  }

  /**
   * Save address
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveAddress(page: Frame|Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.saveAddressButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get selected country by default in form
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getSelectedCountry(page: Page): Promise<string> {
    return this.getTextContent(page, `${this.customerAddressCountryOption}[selected]`, false);
  }
}

export default new AddAddress();
