import BOBasePage from '@pages/BO/BObasePage';

import type CountryData from '@data/faker/country';

import type {Page} from 'playwright';

/**
 * Add country page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCountry extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  public readonly errorMessageIsoCode: string;

  public readonly errorMessagePrefix: string;

  private readonly nameInputEn: string;

  private readonly nameInputFr: string;

  private readonly isoCodeInput: string;

  private readonly callPrefixInput: string;

  private readonly defaultCurrencySelect: string;

  private readonly zoneSelect: string;

  private readonly needZipCodeLabel: (toggle: string) => string;

  private readonly zipCodeFormatInput: string;

  private readonly activeLabel: (toggle: string) => string;

  private readonly containsStatesLabel: (toggle: string) => string;

  private readonly needIdentificationNumberLabel: (toggle: string) => string;

  private readonly displayTaxLabel: (toggle: string) => string;

  private readonly saveCountryButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add country page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Countries > Add new •';
    this.pageTitleEdit = 'Edit: ';
    this.errorMessageIsoCode = 'This ISO code already exists.You cannot create two countries with the same ISO code.';
    this.errorMessagePrefix = 'The call_prefix field is invalid.';

    // Selectors
    this.nameInputEn = '#name_1';
    this.nameInputFr = '#name_2';
    this.isoCodeInput = '#iso_code';
    this.callPrefixInput = '#call_prefix';
    this.defaultCurrencySelect = '#id_currency';
    this.zoneSelect = '#id_zone';
    this.needZipCodeLabel = (toggle: string) => `#need_zip_code_${toggle}`;
    this.zipCodeFormatInput = '#zip_code_format';
    this.activeLabel = (toggle: string) => `#active_${toggle}`;
    this.containsStatesLabel = (toggle: string) => `#contains_states_${toggle}`;
    this.needIdentificationNumberLabel = (toggle: string) => `#need_identification_number_${toggle}`;
    this.displayTaxLabel = (toggle: string) => `#display_tax_label_${toggle}`;
    this.saveCountryButton = '#country_form_submit_btn';
  }

  /*
  Methods
   */
  /**
   * Fill form for add/edit country
   * @param page {Page} Browser tab
   * @param countryData {CountryData} Data to set on new country form
   * @returns {Promise<string>}
   */
  async createEditCountry(page: Page, countryData: CountryData): Promise<string> {
    await this.setValue(page, this.nameInputEn, countryData.name);
    await this.setValue(page, this.isoCodeInput, countryData.isoCode);
    await this.setValue(page, this.callPrefixInput, countryData.callPrefix.toString());
    await this.selectByVisibleText(page, this.defaultCurrencySelect, countryData.currency);
    await this.selectByVisibleText(page, this.zoneSelect, countryData.zone);
    await this.setChecked(page, this.needZipCodeLabel(countryData.needZipCode ? 'on' : 'off'));
    await this.setValue(page, this.zipCodeFormatInput, countryData.zipCodeFormat);
    await this.setChecked(page, this.activeLabel(countryData.active ? 'on' : 'off'));
    await this.setChecked(page, this.containsStatesLabel(countryData.containsStates ? 'on' : 'off'));
    await this.setChecked(
      page,
      this.needIdentificationNumberLabel(countryData.needIdentificationNumber ? 'on' : 'off'),
    );
    await this.setChecked(page, this.displayTaxLabel(countryData.displayTaxNumber ? 'on' : 'off'));
    // Save country
    await this.clickAndWaitForNavigation(page, this.saveCountryButton);

    return this.getTextContent(page, this.alertBlock);
  }

  /**
   * Get the value of an input
   * @param page {Page} Browser tab
   * @param input {string} ID of the input
   * @returns {Promise<string>}
   */
  async getInputValue(page: Page, input: string): Promise<string> {
    let selector: string;

    switch (input) {
      case 'call_prefix':
        selector = this.callPrefixInput;
        break;
      case 'iso_code':
        selector = this.isoCodeInput;
        break;
      case 'zipCodeFormat':
        selector = this.zipCodeFormatInput;
        break;
      case 'nameEn':
        selector = this.nameInputEn;
        break;
      case 'nameFr':
        selector = this.nameInputFr;
        break;
      default:
        throw new Error(`Field ${input} was not found`);
    }

    return page.inputValue(selector);
  }

  /**
   * Get the value of a select
   * @param page {Page} Browser tab
   * @param input {string} ID of the input
   * @returns {Promise<string>}
   */
  async getSelectValue(page: Page, input: string): Promise<string> {
    let selector: string;

    switch (input) {
      case 'id_currency':
        selector = this.defaultCurrencySelect;
        break;
      case 'id_zone':
        selector = this.zoneSelect;
        break;
      default:
        throw new Error(`Field ${input} was not found`);
    }

    return page.$eval(selector, (node: HTMLSelectElement) => node.value);
  }

  /**
   * Get the value of an input
   * @param page {Page} Browser tab
   * @param input {string} ID of the input
   * @returns {Promise<string>}
   */
  async isCheckboxChecked(page: Page, input: string): Promise<boolean> {
    let selector: string;

    switch (input) {
      case 'active':
        selector = this.activeLabel('on');
        break;
      case 'contains_states':
        selector = this.containsStatesLabel('on');
        break;
      case 'display_tax_label':
        selector = this.displayTaxLabel('on');
        break;
      case 'need_identification_number':
        selector = this.needIdentificationNumberLabel('on');
        break;
      case 'need_zip_code':
        selector = this.needZipCodeLabel('on');
        break;
      default:
        throw new Error(`Field ${input} was not found`);
    }

    return this.isChecked(page, selector);
  }
}

export default new AddCountry();
