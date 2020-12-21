require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopParamsGeneral extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @return {Promise<void>}
   */
  async goToSubTabMaintenance(page) {
    await this.clickAndWaitForNavigation(page, this.maintenanceNavItemLink);
  }

  /**
   * Enable/Disable display suppliers
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplaySuppliers(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.displaySuppliersLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable display brands
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayBrands(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.displayBrandsLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable multi store
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setMultiStoreStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.enableMultiStoreLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ShopParamsGeneral();
