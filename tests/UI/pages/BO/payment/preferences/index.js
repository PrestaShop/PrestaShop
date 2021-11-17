require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * BO Payment preferences page, contains texts, selectors and functions to use on the page.
 * @class
 * @extends BOBasePage
 */
class Preferences extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super();

    this.pageTitle = 'Preferences •';

    // Selectors for currency restrictions
    this.euroCurrencyRestrictionsCheckbox = paymentModule => `#form_currency_restrictions_${paymentModule}_0`;
    this.currencyRestrictionsSaveButton = '#form-currency-restrictions-save-button';
    // Selectors for group restrictions
    this.paymentModuleCheckbox = (paymentModule, groupID) => `#form_group_restrictions_${paymentModule}_${groupID}`;
    this.countryRestrictionsCheckbox = (paymentModule, countryID) => '#form_country_restrictions_'
      + `${paymentModule}_${countryID}`;
    this.groupRestrictionsSaveButton = '#form-group-restrictions-save-button';
    // Selectors fot carrier restriction
    this.carrierRestrictionsCheckbox = (paymentModule, carrierID) => '#form_carrier_restrictions_'
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
  async setCurrencyRestriction(page, paymentModule, valueWanted) {
    await this.waitForAttachedSelector(
      page,
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
    );

    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.euroCurrencyRestrictionsCheckbox(paymentModule)} + i`, el => el.click());
    }
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
  async setGroupRestrictions(page, group, paymentModule, valueWanted) {
    const selector = this.paymentModuleCheckbox(paymentModule, group);
    await this.waitForAttachedSelector(page, `${selector} + i`);
    const isCheckboxSelected = await this.isCheckboxSelected(page, selector);

    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${selector} + i`, el => el.click());
    }
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
  async setCountryRestriction(page, countryID, paymentModule, valueWanted) {
    await this.waitForAttachedSelector(
      page,
      `${this.countryRestrictionsCheckbox(paymentModule, countryID)} + i`,
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.countryRestrictionsCheckbox(paymentModule, countryID),
    );

    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.countryRestrictionsCheckbox(paymentModule, countryID)} + i`, el => el.click());
    }
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
  async setCarrierRestriction(page, carrierID, paymentModule, valueWanted) {
    await this.waitForAttachedSelector(
      page,
      `${this.carrierRestrictionsCheckbox(paymentModule, carrierID)} + i`,
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.carrierRestrictionsCheckbox(paymentModule, carrierID),
    );

    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.carrierRestrictionsCheckbox(paymentModule, carrierID)} + i`, el => el.click());
    }
    await page.click(this.carrierRestrictionSaveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new Preferences();
