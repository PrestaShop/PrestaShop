require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Preferences extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Preferences •';

    // Selectors for currency restrictions
    this.euroCurrencyRestrictionsCheckbox = paymentModule => '#form_payment_module_preferences_currency_restrictions_'
      + `${paymentModule}_0`;
    this.currencyRestrictionsSaveButton = '#main-div div:nth-child(1) > div.card-footer button';
    // Selectors for group restrictions
    this.paymentModuleCheckbox = (paymentModule, groupID) => '#form_payment_module_preferences_group_restrictions_'
      + `${paymentModule}_${groupID}`;
    this.groupRestrictionsSaveButton = '#main-div div:nth-child(2) > div.card-footer button';
  }

  /*
  Methods
   */
  /**
   * Set currency restrictions
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setCurrencyRestriction(paymentModule, valueWanted) {
    await this.waitForVisibleSelector(
      `${this.euroCurrencyRestrictionsCheckbox(paymentModule)} + i`,
    );
    const isCheckboxSelected = await this.isCheckboxSelected(
      this.euroCurrencyRestrictionsCheckbox(paymentModule),
    );
    if (valueWanted !== isCheckboxSelected) {
      await this.page.click(`${this.euroCurrencyRestrictionsCheckbox(paymentModule)} + i`);
    }
    await this.page.click(this.currencyRestrictionsSaveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set group restrictions
   * @param group
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string>}
   */
  async setGroupRestrictions(group, paymentModule, valueWanted) {
    const selector = this.paymentModuleCheckbox(paymentModule, group);
    await this.waitForVisibleSelector(`${selector} + i`);
    const isCheckboxSelected = await this.isCheckboxSelected(selector);
    if (valueWanted !== isCheckboxSelected) {
      await this.page.click(`${selector} + i`);
    }
    await this.page.click(this.groupRestrictionsSaveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
