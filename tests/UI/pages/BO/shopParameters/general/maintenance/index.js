require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Maintenance page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ShopParamsMaintenance extends BOBasePage {
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
    this.shopStatusToggleInput = toggle => `#form_enable_shop_${toggle}`;
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
  async changeShopStatus(page, toEnable = true) {
    await page.check(this.shopStatusToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update Maintenance text
   * @param page {Page} Browser tab
   * @param text {string} Maintenance text to set
   * @return {Promise<string>}
   */
  async changeMaintenanceTextShopStatus(page, text) {
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputEN, text);
    await page.click(this.customMaintenanceFrTab);
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputFR, text);
    await page.click(this.saveFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add my IP address in maintenance IP input
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async addMyIpAddress(page) {
    await page.click(this.addMyIPAddressButton);
    await page.click(this.saveFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add maintenance IP address input
   * @param page {Page} Browser tab
   * @param ipAddress {string} Maintenance IP address to set
   * @return {Promise<string>}
   */
  async addMaintenanceIPAddress(page, ipAddress) {
    await this.setValue(page, this.maintenanceIpInput, ipAddress);
    await page.click(this.saveFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ShopParamsMaintenance();
