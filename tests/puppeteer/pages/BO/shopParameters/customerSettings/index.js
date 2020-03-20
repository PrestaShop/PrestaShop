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
    this.enablePartnerOfferLabel = 'label[for=\'form_general_enable_offers_%TOGGLE\']';
    this.sendEmailAfterRegistrationLabel = 'label[for=\'form_general_send_email_after_registration_%TOGGLE\']';
    this.askForBirthDateLabel = 'label[for=\'form_general_ask_for_birthday_%TOGGLE\']';
    this.enableB2BModeToggle = 'label[for=\'form_general_enable_b2b_mode_%TOGGLE\']';
    this.saveGeneralFormButton = `${this.generalForm} .card-footer button`;
  }

  /*
    Methods
  */
  /**
   * Set option status
   * @param option, option to enable or disable
   * @param toEnable, value wanted
   * @return {Promise<string>}
   */
  async setOptionStatus(option, toEnable = true) {
    switch (option) {
      case 'B2B mode':
        await this.waitForSelectorAndClick(this.enableB2BModeToggle.replace('%TOGGLE', toEnable ? 1 : 0));
        break;
      case 'Partner offers':
        await this.waitForSelectorAndClick(this.enablePartnerOfferLabel.replace('%TOGGLE', toEnable ? 1 : 0));
        break;
      case 'Ask for birth date':
        await this.waitForSelectorAndClick(this.askForBirthDateLabel.replace('%TOGGLE', toEnable ? 1 : 0));
        break;
      case 'Email after registration':
        await this.waitForSelectorAndClick(this.sendEmailAfterRegistrationLabel.replace('%TOGGLE', toEnable ? 1 : 0));
        break;
      case 'Redisplay cart at login':
        await this.waitForSelectorAndClick(this.redisplayCartAtLoginLabel.replace('%TOGGLE', toEnable ? 1 : 0));
        break;
      default:
        throw new Error(`${option} was not found in the page`);
    }
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
