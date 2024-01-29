// Import pages
import FOBasePage from '@pages/FO/classic/FObasePage';

import type {Page} from 'playwright';
import AddressData from '@data/faker/address';

/**
 * Add address page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AddAddressPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly creationFormTitle: string;

  public readonly updateFormTitle: string;

  private readonly pageHeaderTitle: string;

  private readonly addressForm: string;

  private readonly aliasInput: string;

  private readonly firstNameInput: string;

  private readonly lastNameInput: string;

  private readonly companyInput: string;

  private readonly vatNumberInput: string;

  private readonly addressInput: string;

  private readonly secondAddressInput: string;

  private readonly postCodeInput: string;

  private readonly cityInput: string;

  private readonly countrySelect: string;

  private readonly phoneInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add address page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Address';
    this.creationFormTitle = 'New address';
    this.updateFormTitle = 'Update your address';

    // Selectors
    this.pageHeaderTitle = '#main .page-header h1';
    this.addressForm = '.address-form';
    this.aliasInput = `${this.addressForm} input[name=alias]`;
    this.firstNameInput = `${this.addressForm} input[name=firstname]`;
    this.lastNameInput = `${this.addressForm} input[name=lastname]`;
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
  getHeaderTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.pageHeaderTitle);
  }

  /**
   * Fill address form and save
   * @param page {Page} Browser tab
   * @param addressData {AddressData} Address's information to fill on form
   * @returns {Promise<string>}
   */
  async setAddress(page: Page, addressData: AddressData): Promise<string> {
    // Set alias if added (optional)
    if (addressData.alias) {
      await this.setValue(page, this.aliasInput, addressData.alias);
    }

    await this.setValue(page, this.firstNameInput, addressData.firstName);
    await this.setValue(page, this.lastNameInput, addressData.lastName);

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
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Is vat number input is required
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isVatNumberRequired(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.vatNumberInput}:required`, 1000);
  }

  /**
   * Is country exist
   * @param page {Page} Browser tab
   * @param countryName {string} String of the country name
   * @returns {Promise<boolean>}
   */
  async countryExist(page: Page, countryName: string): Promise<boolean> {
    const options: (string|null)[] = await page
      .locator(`${this.countrySelect} option`)
      .allTextContents();

    return options.indexOf(countryName) !== -1;
  }
}

const addAddressPage = new AddAddressPage();
export {addAddressPage, AddAddressPage};
