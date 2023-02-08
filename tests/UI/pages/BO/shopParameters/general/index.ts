import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * General page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ShopParamsGeneral extends BOBasePage {
  public readonly pageTitle: string;

  private readonly maintenanceNavItemLink: string;

  private readonly displaySuppliersToggleInput: (toggle: number) => string;

  private readonly displayBrandsToggleInput: (toggle: number) => string;

  private readonly enableMultiStoreToggleInput: (toggle: number) => string;

  private readonly saveFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on general page
   */
  constructor() {
    super();

    this.pageTitle = 'Preferences •';

    // Selectors
    this.maintenanceNavItemLink = '#subtab-AdminMaintenance';
    this.displaySuppliersToggleInput = (toggle: number) => `#form_display_suppliers_${toggle}`;
    this.displayBrandsToggleInput = (toggle: number) => `#form_display_manufacturers_${toggle}`;
    this.enableMultiStoreToggleInput = (toggle: number) => `#form_multishop_feature_active_${toggle}`;
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
  async goToSubTabMaintenance(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.maintenanceNavItemLink);
  }

  /**
   * Enable/Disable display suppliers
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable suppliers
   * @returns {Promise<string>}
   */
  async setDisplaySuppliers(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.displaySuppliersToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable display brands
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable brands
   * @returns {Promise<string>}
   */
  async setDisplayBrands(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.displayBrandsToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable multi store
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable multistore
   * @returns {Promise<string>}
   */
  async setMultiStoreStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableMultiStoreToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new ShopParamsGeneral();
