require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class shopParamsMaintenance extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Maintenance •';

    // Selectors
    this.generalNavItemLink = '#subtab-AdminPreferences';
    this.disableShopLabel = 'label[for=\'form_general_enable_shop_0\']';
    this.enableShopLabel = 'label[for=\'form_general_enable_shop_1\']';
    this.saveFormButton = 'form .card-footer button';
  }

  /*
  Methods
   */

  /**
   * Change Tab to General in Shop Parameters Maintenance Page
   * @return {Promise<void>}
   */
  async goToSubTabGeneral() {
    await this.page.click(this.generalNavItemLink, {waitUntil: 'networkidle2'});
  }

  /**
   * Enable / disable shop
   * @param toEnable, true to enable and false to disable
   * @return {Promise<void>}
   */
  async changeShopStatus(toEnable = true) {
    if (toEnable) await this.page.click(this.enableShopLabel);
    else await this.page.click(this.disableShopLabel);
    await this.page.click(this.saveFormButton);
    await this.page.waitForSelector(this.alertSuccessBloc);
  }
};
