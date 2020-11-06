require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopParamsMaintenance extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Maintenance •';
    this.maintenanceText = 'We are currently updating our shop and will be back really soon. Thanks for your patience.';

    // Selectors
    this.generalNavItemLink = '#subtab-AdminPreferences';
    this.generalForm = '#form-maintenance';
    this.switchShopLabel = toggle => `label[for='form_enable_shop_${toggle}']`;
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
   * Enable / disable shop
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeShopStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchShopLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Update Maintenance text
   * @param page
   * @param text
   * @return {Promise<string>}
   */
  async changeMaintenanceTextShopStatus(page, text) {
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputEN, text);
    await page.click(this.customMaintenanceFrTab);
    await this.setValueOnTinymceInput(page, this.maintenanceTextInputFR, text);
    await page.click(this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Add my IP address in maintenance IP input
   * @param page
   * @return {Promise<string>}
   */
  async addMyIpAddress(page) {
    await page.click(this.addMyIPAddressButton);
    await page.click(this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Add maintenance IP address input
   * @param page
   * @param ipAddress
   * @return {Promise<string>}
   */
  async addMaintenanceIPAddress(page, ipAddress) {
    await this.setValue(page, this.maintenanceIpInput, ipAddress);
    await page.click(this.saveFormButton);
    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new ShopParamsMaintenance();
