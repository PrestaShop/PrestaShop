require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ShopParamsMaintenance extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Maintenance •';
    this.maintenanceText = 'We are currently updating our shop and will be back really soon. Thanks for your patience.';

    // Selectors
    this.generalNavItemLink = '#subtab-AdminPreferences';
    this.switchShopLabel = toggle => `label[for='form_general_enable_shop_${toggle}']`;
    this.maintenanceTextInputEN = '#form_general_maintenance_text_1_ifr';
    this.customMaintenanceFrTab = '#form_general_maintenance_text a[data-locale=\'fr\']';
    this.maintenanceTextInputFR = '#form_general_maintenance_text_2_ifr';
    this.addMyIPAddressButton = 'form .add_ip_button';
    this.maintenanceIpInput = '#form_general_maintenance_ip';
    this.saveFormButton = 'form .card-footer button';
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
    return this.getAlertSuccessBlockParagraphContent(page);
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add my IP address in maintenance IP input
   * @param page
   * @return {Promise<string>}
   */
  async addMyIpAddress(page) {
    await page.click(this.addMyIPAddressButton);
    await page.click(this.saveFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
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
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ShopParamsMaintenance();
