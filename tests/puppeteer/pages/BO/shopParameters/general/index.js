require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class shopParamsGeneral extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Preferences •';

    // Selectors
    this.maintenanceNavItemLink = '#subtab-AdminMaintenance';
    this.configurationForm = '#configuration_form';
    this.displaySuppliersLabel = toggle => `label[for='form_general_display_suppliers_${toggle}']`;
    this.displayBrandsLabel = toggle => `label[for='form_general_display_manufacturers_${toggle}']`;
    this.enableMultiStoreLabel = toggle => `label[for='form_general_multishop_feature_active_${toggle}']`;
    this.saveFormButton = `${this.configurationForm} .card-footer button`;
  }

  /*
  Methods
   */

  /**
   * Change Tab to Maintenance in Shop Parameters General Page
   * @return {Promise<void>}
   */
  async goToSubTabMaintenance() {
    await this.clickAndWaitForNavigation(this.maintenanceNavItemLink);
  }

  /**
   * Enable/Disable display suppliers
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplaySuppliers(toEnable = true) {
    await this.waitForSelectorAndClick(this.displaySuppliersLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable display brands
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayBrands(toEnable = true) {
    await this.waitForSelectorAndClick(this.displayBrandsLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable multi store
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setMultiStoreStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableMultiStoreLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
