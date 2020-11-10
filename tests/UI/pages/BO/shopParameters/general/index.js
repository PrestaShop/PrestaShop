require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopParamsGeneral extends BOBasePage {
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
    await page.check(this.displaySuppliersToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Enable/Disable display brands
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayBrands(page, toEnable = true) {
    await page.check(this.displayBrandsToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Enable/Disable multi store
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setMultiStoreStatus(page, toEnable = true) {
    await page.check(this.enableMultiStoreToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new ShopParamsGeneral();
