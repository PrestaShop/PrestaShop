require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCurrency extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Currencies • ';

    // Selectors
    this.currencySelect = '#currency_selected_iso_code';
    this.selectResultOption = 'li.select2-results__option:nth-child(%ID)';
    this.alternativeCurrencyCheckBox = '#currency_unofficial + i';
    this.currencyNameInput = '#currency_names_%ID';
    this.symbolInput = '#currency_symbols_%ID';
    this.isoCodeInput = '#currency_iso_code';
    this.exchangeRateInput = '#currency_exchange_rate';
    this.decimalsInput = '#currency_precision';
    this.statusSwitch = 'label[for=\'currency_active_%ID\']';
    this.resetDefaultSettingsButton = '#currency_reset_default_settings';
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
    await this.page.select(this.currencySelect, currencyData.isoCode);
    await this.page.waitForSelector(`${this.currencyLoadingModal}.show`, {visible: true});
    // Waiting for currency to be loaded : 10 sec max
    // To check if modal still exist
    let displayed = false;
    for (let i = 0; i < 50 && !displayed; i++) {
      displayed = await this.page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector))
          .getPropertyValue('display') === 'none',
        this.currencyLoadingModal,
      );
      await this.page.waitFor(200);
    }
    await this.page.click(this.statusSwitch.replace('%ID', currencyData.enabled ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
