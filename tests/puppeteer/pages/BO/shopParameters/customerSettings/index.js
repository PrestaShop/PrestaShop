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
    this.enablePartnerOfferLabel = 'label[for=\'form_general_enable_offers_%TOGGLE\']';
    this.sendEmailAfterRegistrationLabel = 'label[for=\'form_general_send_email_after_registration_%TOGGLE\']';
    this.askForBirthDateLabel = 'label[for=\'form_general_ask_for_birthday_%TOGGLE\']';
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
  async setRedisplayCartAtLoginStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.redisplayCartAtLoginLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable send email after registration
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setEmailAfterRegistrationStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.sendEmailAfterRegistrationLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable ask for birth date
   * @param toEnable
   * @returns {Promise<string|*>}
   */
  async setAskForBirthDate(toEnable = true) {
    await this.waitForSelectorAndClick(this.askForBirthDateLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable partner offer
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setEnablePartnerOffer(toEnable = true){
    await this.waitForSelectorAndClick(this.enablePartnerOfferLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
