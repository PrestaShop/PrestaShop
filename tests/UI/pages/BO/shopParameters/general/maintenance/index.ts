import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Maintenance page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ShopParamsMaintenance extends BOBasePage {
  public readonly pageTitle: string;

  public readonly maintenanceText: string;

  private readonly generalForm: string;

  private readonly shopStatusToggleInput: (toggle: number) => string;

  private readonly allowAdminsToggleInput: (toggle: number) => string;

  private readonly maintenanceTextInputEN: string;

  private readonly customMaintenanceFrTab: string;

  private readonly maintenanceTextInputFR: string;

  private readonly addMyIPAddressButton: string;

  private readonly maintenanceIpInput: string;

  private readonly saveFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on maintenance page
   */
  constructor() {
    super();

    this.pageTitle = 'Maintenance •';
    this.maintenanceText = 'We are currently updating our shop and will be back really soon. Thanks for your patience.';

    // Selectors
    this.generalForm = '#form-maintenance';
    this.shopStatusToggleInput = (toggle: number) => `#form_enable_shop_${toggle}`;
    this.allowAdminsToggleInput = (toggle: number) => `#form_maintenance_allow_admins_${toggle}`;
    this.maintenanceTextInputEN = '#form_maintenance_text_1_ifr';
    this.customMaintenanceFrTab = `${this.generalForm} a[data-locale='fr']`;
    this.maintenanceTextInputFR = '#form_maintenance_text_2_ifr';
    this.addMyIPAddressButton = `${this.generalForm} .add_ip_button`;
    this.maintenanceIpInput = '#form_maintenance_ip';
    this.saveFormButton = '#form-maintenance-save-button';
  }

  /*
  Methods
   */
  /**
   * Enable/Disable shop
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable the shop
   * @return {Promise<string>}
   */
  async changeShopStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.shopStatusToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    await this.elementNotVisible(page, this.shopStatusToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable store for logged-in employees
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to enable/disable the shop
   * @return {Promise<string>}
   */
  async changeStoreForLoggedInEmployees(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.allowAdminsToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveFormButton);
    await this.elementNotVisible(page, this.allowAdminsToggleInput(!toEnable ? 1 : 0), 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update Maintenance text
   * @param page {Page} Browser tab
   * @param text {string} Maintenance text to set
   * @return {Promise<string>}
   */
  async changeMaintenanceTextShopStatus(page: Page, text: string): Promise<string> {
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputEN, text);
    await page.locator(this.customMaintenanceFrTab).click();
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputFR, text);
    await page.locator(this.saveFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add my IP address in maintenance IP input
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async addMyIpAddress(page: Page): Promise<string> {
    await page.locator(this.addMyIPAddressButton).click();
    await page.locator(this.saveFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add maintenance IP address input
   * @param page {Page} Browser tab
   * @param ipAddress {string} Maintenance IP address to set
   * @return {Promise<string>}
   */
  async addMaintenanceIPAddress(page: Page, ipAddress: string): Promise<string> {
    await this.setValue(page, this.maintenanceIpInput, ipAddress);
    await page.locator(this.saveFormButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new ShopParamsMaintenance();
