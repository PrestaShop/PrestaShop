import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * BO Payment preferences page, contains texts, selectors and functions to use on the page.
 * @class
 * @extends BOBasePage
 */
class Preferences extends BOBasePage {
  public readonly pageTitle: string;

  private readonly euroCurrencyRestrictionsCheckbox: (paymentModule: string) => string;

  private readonly currencyRestrictionsSaveButton: string;

  private readonly paymentModuleCheckbox: (paymentModule: string, groupID: string) => string;

  private readonly countryRestrictionsCheckbox: (paymentModule: string, countryID: number) => string;

  private readonly groupRestrictionsSaveButton: string;

  private readonly carrierRestrictionsCheckbox: (paymentModule: string, carrierID: number) => string;

  private readonly carrierRestrictionSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super();

    this.pageTitle = 'Preferences •';

    // Selectors for currency restrictions
    this.euroCurrencyRestrictionsCheckbox = (paymentModule: string) => `#form_currency_restrictions_${paymentModule}_0`;
    this.currencyRestrictionsSaveButton = '#form-currency-restrictions-save-button';
    // Selectors for group restrictions
    this.paymentModuleCheckbox = (paymentModule: string, groupID: string) => `#form_group_restrictions_${paymentModule}`
      + `_${groupID}`;
    this.countryRestrictionsCheckbox = (paymentModule: string, countryID: number) => '#form_country_restrictions_'
      + `${paymentModule}_${countryID}`;
    this.groupRestrictionsSaveButton = '#form-group-restrictions-save-button';
    // Selectors fot carrier restriction
    this.carrierRestrictionsCheckbox = (paymentModule: string, carrierID: number) => '#form_carrier_restrictions_'
      + `${paymentModule}_${carrierID}`;
    this.carrierRestrictionSaveButton = '#form-carrier-restrictions-save-button';
  }

  /*
  Methods
   */
  /**
   * Set currency restrictions
   * @param page {Page} Browser tab
   * @param paymentModule {string} Name of the module to set restriction on
   * @param valueWanted {boolean} True to allow the module for the currency
   * @returns {Promise<string>}
   */
  async setCurrencyRestriction(page: Page, paymentModule: string, valueWanted: boolean): Promise<string> {
    await this.waitForAttachedSelector(
      page,
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
    );

    await this.setCheckedWithIcon(page, this.euroCurrencyRestrictionsCheckbox(paymentModule), valueWanted);

    await page.click(this.currencyRestrictionsSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set group restrictions
   * @param page {Page} Browser tab
   * @param group {string} String of the group
   * @param paymentModule {string} Name of the module to set restriction on
   * @param valueWanted {boolean} True to allow the module for the group
   * @returns {Promise<string>}
   */
  async setGroupRestrictions(page: Page, group: string, paymentModule: string, valueWanted: boolean): Promise<string> {
    await this.waitForAttachedSelector(page, `${this.paymentModuleCheckbox(paymentModule, group)} + i`);
    await this.setCheckedWithIcon(page, this.paymentModuleCheckbox(paymentModule, group), valueWanted);

    await page.click(this.groupRestrictionsSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set country restrictions
   * @param page {Page} Browser tab
   * @param countryID {number} Country position on the table
   * @param paymentModule {string} Name of the module to set restriction on
   * @param valueWanted {boolean} True to allow the module for the country
   * @returns {Promise<string>}
   */
  async setCountryRestriction(page: Page, countryID: number, paymentModule: string, valueWanted: boolean): Promise<string> {
    await this.waitForAttachedSelector(
      page,
      `${this.countryRestrictionsCheckbox(paymentModule, countryID)} + i`,
    );
    await this.setCheckedWithIcon(page, this.countryRestrictionsCheckbox(paymentModule, countryID), valueWanted);

    await page.click(this.currencyRestrictionsSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set carrier restriction
   * @param page {Page} Browser tab
   * @param carrierID {number} Carrier position on the table
   * @param paymentModule {string} Name of the module to set restriction on
   * @param valueWanted {boolean} True to allow the module for the carrier
   * @return {Promise<string>}
   */
  async setCarrierRestriction(page: Page, carrierID: number, paymentModule: string, valueWanted: boolean): Promise<string> {
    await this.waitForAttachedSelector(
      page,
      `${this.carrierRestrictionsCheckbox(paymentModule, carrierID)} + i`,
    );
    await this.setCheckedWithIcon(page, this.carrierRestrictionsCheckbox(paymentModule, carrierID), valueWanted);

    await page.click(this.carrierRestrictionSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Preferences();
