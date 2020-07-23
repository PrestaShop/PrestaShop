require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Preferences extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Preferences •';

    // Selectors for currency restrictions
    this.euroCurrencyRestrictionsCheckbox = paymentModule => '#form_payment_module_preferences_currency_restrictions_'
      + `${paymentModule}_0`;
    this.currencyRestrictionsSaveButton = '#main-div div:nth-child(1) > div.card-footer button';
    // Selectors for group restrictions
    this.paymentModuleCheckbox = (paymentModule, groupID) => '#form_payment_module_preferences_group_restrictions_'
      + `${paymentModule}_${groupID}`;
    this.countryRestrictionsCheckbox = (paymentModule, countryID) => '#form_payment_module_preferences_country_'
      + `restrictions_${paymentModule}_${countryID}`;
    this.groupRestrictionsSaveButton = '#main-div div:nth-child(2) > div.card-footer button';
    // Selectors fot carrier restriction
    this.carrierRestrictionsCheckbox = (paymentModule, carrierID) => '#form_payment_module_preferences_carrier_'
     + `restrictions_${paymentModule}_${carrierID}`;
    this.carrierRestrictionSaveButton = 'div.card:nth-child(4) .card-footer button';
  }

  /*
  Methods
   */
  /**
   * Set currency restrictions
   * @param page
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setCurrencyRestriction(page, paymentModule, valueWanted) {
    await page.waitForSelector(
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
      {state: 'attached'},
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
    );
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.euroCurrencyRestrictionsCheckbox(paymentModule)} + i`, el => el.click());
    }
    await page.click(this.currencyRestrictionsSaveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Set group restrictions
   * @param page
   * @param group
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setGroupRestrictions(page, group, paymentModule, valueWanted) {
    const selector = this.paymentModuleCheckbox(paymentModule, group);
    await page.waitForSelector(`${selector} + i`, {state: 'attached'});
    const isCheckboxSelected = await this.isCheckboxSelected(page, selector);
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${selector} + i`, el => el.click());
    }
    await page.click(this.groupRestrictionsSaveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Set country restrictions
   * @param page
   * @param countryID
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setCountryRestriction(page, countryID, paymentModule, valueWanted) {
    await page.waitForSelector(
      `${this.countryRestrictionsCheckbox(paymentModule, countryID)} + i`,
      {state: 'attached'},
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.countryRestrictionsCheckbox(paymentModule, countryID),
    );
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.countryRestrictionsCheckbox(paymentModule, countryID)} + i`, el => el.click());
    }
    await page.click(this.currencyRestrictionsSaveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Set carrier restriction
   * @param page
   * @param carrierID
   * @param paymentModule
   * @param valueWanted
   * @return {Promise<string>}
   */
  async setCarrierRestriction(page, carrierID, paymentModule, valueWanted) {
    await page.waitForSelector(
      `${this.carrierRestrictionsCheckbox(paymentModule, carrierID)} + i`,
      {state: 'attached'},
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      page,
      this.carrierRestrictionsCheckbox(paymentModule, carrierID),
    );
    if (valueWanted !== isCheckboxSelected) {
      await page.$eval(`${this.carrierRestrictionsCheckbox(paymentModule, carrierID)} + i`, el => el.click());
    }
    await page.click(this.carrierRestrictionSaveButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new Preferences();
