require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class shopParamsMaintenance extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Maintenance •';
    this.maintenanceText = 'We are currently updating our shop and will be back really soon. Thanks for your patience.';

    // Selectors
    this.generalNavItemLink = '#subtab-AdminPreferences';
    this.switchShopLabel = 'label[for=\'form_general_enable_shop_%TOGGLE\']';
    this.maintenanceTextInputEN = '#form_general_maintenance_text_1_ifr';
    this.customMaintenanceFrTab = '#form_general_maintenance_text  a[data-locale="fr"]';
    this.maintenanceTextInputFR = '#form_general_maintenance_text_2_ifr';
    this.addMyIPAddressButton = 'form .add_ip_button';
    this.maintenanceIpInput = '#form_general_maintenance_ip';
    this.saveFormButton = 'form .card-footer button';
  }

  /*
  Methods
   */

  /**
   * Change Tab to general in Shop Parameters Maintenance Page
   * @return {Promise<void>}
   */
  async goToSubTabGeneral() {
    await this.page.click(this.generalNavItemLink, {waitUntil: 'networkidle2'});
  }

  /**
   * Enable / disable shop
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeShopStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchShopLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Update Maintenance text
   * @param text
   * @return {Promise<string>}
   */
  async changeMaintenanceTextShopStatus(text) {
    await this.setValueOnTinymceInput(this.maintenanceTextInputEN, text);
    await this.page.click(this.customMaintenanceFrTab);
    await this.setValueOnTinymceInput(this.maintenanceTextInputFR, text);
    await this.page.click(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Add my IP address in maintenance IP input
   * @return {Promise<string>}
   */
  async addMyIpAddress() {
    await this.page.click(this.addMyIPAddressButton);
    await this.page.click(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }

  /**
   * Add maintenance IP address input
   * @param ipAddress
   * @return {Promise<string>}
   */
  async addMaintenanceIPAddress(ipAddress) {
    await this.setValue(this.maintenanceIpInput, ipAddress);
    await this.page.click(this.saveFormButton);
    return this.getTextContent(this.alertSuccessBloc);
  }
};
