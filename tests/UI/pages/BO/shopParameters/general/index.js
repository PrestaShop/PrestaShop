require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * General page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ShopParamsGeneral extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on general page
   */
  constructor() {
    super();

    this.pageTitle = 'Preferences •';

    // Selectors
    this.maintenanceNavItemLink = '#subtab-AdminMaintenance';
    this.displaySuppliersToggleInput = toggle => `#form_display_suppliers_${toggle}`;
    this.displayBrandsToggleInput = toggle => `#form_display_manufacturers_${toggle}`;
    this.enableMultiStoreToggleInput = toggle => `#form_multishop_feature_active_${toggle}`;
    this.saveFormButton = '#form-preferences-save-button';
  }

  /*
  Methods
   */

  /**
   * Change Tab to Maintenance in Shop Parameters General Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabMaintenance(page) {
    await this.clickAndWaitForNavigation(page, this.maintenanceNavItemLink);
  }

  /**
   * Enable/Disable display suppliers
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable suppliers
   * @returns {Promise<string>}
   */
  async setDisplaySuppliers(page, toEnable = true) {
    await page.check(this.displaySuppliersToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable display brands
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable brands
   * @returns {Promise<string>}
   */
  async setDisplayBrands(page, toEnable = true) {
    await page.check(this.displayBrandsToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable multi store
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable multistore
   * @returns {Promise<string>}
   */
  async setMultiStoreStatus(page, toEnable = true) {
    await page.check(this.enableMultiStoreToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ShopParamsGeneral();
