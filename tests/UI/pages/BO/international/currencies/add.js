require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCurrency extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Currencies • ';

    // Selectors
    this.currencySelect = '#currency_selected_iso_code';
    this.alternativeCurrencyCheckBox = '#currency_unofficial';
    this.currencyNameInput = id => `#currency_names_${id}`;
    this.isoCodeInput = '#currency_iso_code';
    this.exchangeRateInput = '#currency_exchange_rate';
    this.statusSwitch = id => `label[for='currency_active_${id}']`;
    this.saveButton = 'div.card-footer button[type=\'submit\']';

    // currency modal
    this.currencyLoadingModal = '#currency_loading_data_modal';
  }

  /*
  Methods
   */

  /**
   * Add official currency
   * @param currencyData, currency to add
   * @return {Promise<textContent>}, successful text message that appears
   */
  async addOfficialCurrency(currencyData) {
    // Select currency
    await this.page.selectOption(this.currencySelect, currencyData.isoCode);
    await this.waitForVisibleSelector(`${this.currencyLoadingModal}.show`);
    // Waiting for currency to be loaded : 10 sec max
    // To check if modal still exist
    let displayed = false;
    for (let i = 0; i < 50 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await this.page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector))
          .getPropertyValue('display') === 'none',
        this.currencyLoadingModal,
      );
      await this.page.waitForTimeout(200);
    }

    await this.page.click(this.statusSwitch(currencyData.enabled ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Create unofficial currency
   * @param currencyData
   * @return {Promise<textContent>}
   */
  async createUnOfficialCurrency(currencyData) {
    if (!(await this.isCheckboxSelected(this.alternativeCurrencyCheckBox))) {
      await this.page.$eval(`${this.alternativeCurrencyCheckBox} + i`, el => el.click());
    }
    await this.setValue(this.currencyNameInput(1), currencyData.name);
    await this.setValue(this.isoCodeInput, currencyData.isoCode);
    await this.setValue(this.exchangeRateInput, currencyData.exchangeRate.toString());
    await this.page.click(this.statusSwitch(currencyData.enabled ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
