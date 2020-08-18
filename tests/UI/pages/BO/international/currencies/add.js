require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddCurrency extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Currencies • ';

    // Selectors
    this.currencySelect = '#currency_selected_iso_code';
    this.alternativeCurrencyCheckBox = '#currency_unofficial';
    this.currencyNameInput = id => `#currency_names_${id}`;
    this.isoCodeInput = '#currency_iso_code';
    this.exchangeRateInput = '#currency_exchange_rate';
    this.statusSwitch = id => `label[for='currency_active_${id}']`;
    this.saveButton = '#save-button';

    // currency modal
    this.currencyLoadingModal = '#currency_loading_data_modal';
  }

  /*
  Methods
   */

  /**
   * Add official currency
   * @param page
   * @param currencyData, currency to add
   * @returns {Promise<string>}, successful text message that appears
   */
  async addOfficialCurrency(page, currencyData) {
    // Select currency
    await page.selectOption(this.currencySelect, currencyData.isoCode);
    await this.waitForVisibleSelector(page, `${this.currencyLoadingModal}.show`);
    // Waiting for currency to be loaded : 10 sec max
    // To check if modal still exist
    let displayed = false;
    for (let i = 0; i < 50 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector))
          .getPropertyValue('display') === 'none',
        this.currencyLoadingModal,
      );
      await page.waitForTimeout(200);
    }

    await page.click(this.statusSwitch(currencyData.enabled ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Create unofficial currency
   * @param page
   * @param currencyData
   * @returns {Promise<string>}
   */
  async createUnOfficialCurrency(page, currencyData) {
    if (!(await this.isCheckboxSelected(page, this.alternativeCurrencyCheckBox))) {
      await page.$eval(`${this.alternativeCurrencyCheckBox} + i`, el => el.click());
    }
    await this.setValue(page, this.currencyNameInput(1), currencyData.name);
    await this.setValue(page, this.isoCodeInput, currencyData.isoCode);
    await this.setValue(page, this.exchangeRateInput, currencyData.exchangeRate.toString());
    await page.click(this.statusSwitch(currencyData.enabled ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Update exchange rate
   * @param page
   * @param value
   * @returns {Promise<string>}
   */
  async updateExchangeRate(page, value) {
    await this.setValue(page, this.exchangeRateInput, value.toString());
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new AddCurrency();
