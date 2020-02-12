require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class productSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Product Settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Products general form
    this.productGeneralForm = '#configuration_fieldset_products';
    this.switchCatalogModeLabel = 'label[for=\'form_general_catalog_mode_%TOGGLE\']';
    this.switchShowPricesLabel = 'label[for=\'form_general_catalog_mode_with_prices_%TOGGLE\']';
    this.newDaysNumberInput = '#form_general_new_days_number';
    this.saveProductGeneralFormButton = `${this.productGeneralForm} .card-footer button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable catalog mode
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeCatalogModeStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchCatalogModeLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Enable/disable show prices
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeShowPricesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchShowPricesLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Update number of days
   * @param numberOfDays
   * @returns {Promise<string|*>}
   */
  async updateNumberOfDays(numberOfDays) {
    await this.setValue(this.newDaysNumberInput, numberOfDays.toString());
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }
};
