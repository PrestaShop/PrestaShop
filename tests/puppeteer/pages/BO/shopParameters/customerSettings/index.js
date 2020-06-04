require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class customerSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Customers •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    this.generalForm = '#configuration_form';
    this.redisplayCartAtLoginLabel = 'label[for=\'form_general_redisplay_cart_at_login_%TOGGLE\']';
    this.sedEmailAfterRegistration = 'label[for=\'form_general_send_email_after_registration_%TOGGLE\']';
    this.saveGeneralFormButton = `${this.generalForm} .card-footer button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable redisplay cart at login
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setRedisplayCartAtLogin(toEnable = true) {
    await this.waitForSelectorAndClick(this.redisplayCartAtLoginLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable send email after registration
   * @param toEnable
   * @returns {Promise<string|*>}
   */
  async setEmailAfterRegistration(toEnable = true) {
    await this.waitForSelectorAndClick(this.sedEmailAfterRegistration.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
