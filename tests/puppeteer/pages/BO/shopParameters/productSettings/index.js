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
    this.maxSizeShortDescriptionInput = '#form_general_short_description_limit';
    this.newDaysNumberInput = '#form_general_new_days_number';
    this.switchForceUpdateFriendlyURLLabel = 'label[for=\'form_general_force_friendly_url_%TOGGLE\']';
    this.switchDefaultActivationStatusLabel = 'label[for=\'form_general_default_status_%TOGGLE\']';
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
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/disable show prices
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeShowPricesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchShowPricesLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Update number of days
   * @param numberOfDays
   * @returns {Promise<string|*>}
   */
  async updateNumberOfDays(numberOfDays) {
    await this.setValue(this.newDaysNumberInput, numberOfDays.toString());
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Update max size of short description
   * @param size
   * @returns {Promise<string|*>}
   */
  async UpdateMaxSizeOfSummary(size) {
    await this.setValue(this.maxSizeShortDescriptionInput, size.toString());
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable force update of friendly URL
   * @param toEnable
   * @returns {Promise<string|*>}
   */
  async setForceUpdateFriendlyURL(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchForceUpdateFriendlyURLLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Change default activation status
   * @param toEnable
   * @returns {Promise<string|*>}
   */
  async setDefaultActivationStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchDefaultActivationStatusLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
