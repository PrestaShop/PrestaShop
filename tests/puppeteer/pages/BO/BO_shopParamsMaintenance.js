const BOCommonPage = require('./BO_commonPage');

module.exports = class SHOPPARAMSMAINTENANCE extends BOCommonPage {
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

  async goToSubTabGeneral() {
    await this.page.click(this.generalNavItemLink, {waitUntil: 'networkidle2'});
  }

  async changeShopStatus(toEnable = true) {
    if (toEnable) await this.page.click(this.enableShopLabel);
    else await this.page.click(this.disableShopLabel);
    await this.page.click(this.saveFormButton);
    await this.page.waitForSelector(this.alertSuccessBloc);
  }
};
