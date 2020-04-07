require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');
const {options} = require('@pages/BO/shopParameters/customerSettings/options.js');

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
    let selector;
    switch (option) {
      case options.OPTION_B2B:
        selector = this.enableB2BModeToggle;
        break;
      case options.OPTION_PARTNER_OFFER:
        selector = this.enablePartnerOfferLabel;
        break;
      case options.OPTION_BIRTH_DATE:
        selector = this.askForBirthDateLabel;
        break;
      case options.OPTION_EMAIL_REGISTRATION:
        selector = this.sendEmailAfterRegistrationLabel;
        break;
      case options.OPTION_CART_LOGIN:
        selector = this.redisplayCartAtLoginLabel;
        break;
      default:
        throw new Error(`${option} was not found`);
    }
    await this.waitForSelectorAndClick(selector.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
