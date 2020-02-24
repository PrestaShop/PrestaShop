require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Preferences extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Preferences •';

    // Selectors for currency restrictions
    this.euroCurrencyRestrictionsCheckbox = 'input#form_payment_module_preferences_currency_restrictions_'
      + '%PAYMENTMODULE_0';
    this.currencyRestrictionsSaveButton = '#main-div div:nth-child(1) > div.card-footer  button';
  }

  /*
  Methods
   */
  /**
   * Set currency restrictions
   * @param paymentModule
   * @param valueWanted
   * @returns {Promise<string|*>}
   */
  async setCurrencyRestriction(paymentModule, valueWanted) {
    await this.page.waitForSelector(this.euroCurrencyRestrictionsCheckbox.replace('%PAYMENTMODULE', paymentModule));
    const isCheckboxSelected = await this.isCheckboxSelected(this.euroCurrencyRestrictionsCheckbox.replace(
      '%PAYMENTMODULE', paymentModule,
    ));
    if (valueWanted !== isCheckboxSelected) {
      await this.page.click(`${this.euroCurrencyRestrictionsCheckbox.replace('%PAYMENTMODULE', paymentModule)}+ i`);
    }
    await this.page.click(this.currencyRestrictionsSaveButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
