require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');
const {options} = require('@pages/BO/shopParameters/customerSettings/options.js');

/**
 * Customer settings page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CustomerSettings extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on customer settings page
   */
  constructor() {
    super();

    this.pageTitle = 'Customers •';
    this.successfulUpdateMessage = 'Update successful';

    // Header selectors
    this.titlesSubtab = '#subtab-AdminGenders';
    this.groupsSubtab = '#subtab-AdminGroups';

    // Form selectors
    this.redisplayCartAtLoginToggleInput = toggle => `#form_redisplay_cart_at_login_${toggle}`;
    this.enablePartnerOfferToggleInput = toggle => `#form_enable_offers_${toggle}`;
    this.sendEmailAfterRegistrationToggleInput = toggle => `#form_send_email_after_registration_${toggle}`;
    this.askForBirthDateToggleInput = toggle => `#form_ask_for_birthday_${toggle}`;
    this.enableB2BModeToggle = toggle => `#form_enable_b2b_mode_${toggle}`;
    this.saveGeneralFormButton = '#form-general-save-button';
  }

  /*
    Methods
  */

  /**
   * Click on tab titles
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToTitlesPage(page) {
    await this.clickAndWaitForNavigation(page, this.titlesSubtab);
  }

  /**
   * Click on tab groups
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToGroupsPage(page) {
    await this.clickAndWaitForNavigation(page, this.groupsSubtab);
  }

  /**
   * Set option status
   * @param page {Page} Browser tab
   * @param option {string} Option to enable or disable
   * @param toEnable {boolean} True if we need to enable status
   * @return {Promise<string>}
   */
  async setOptionStatus(page, option, toEnable = true) {
    let selector;

    switch (option) {
      case options.OPTION_B2B:
        selector = this.enableB2BModeToggle;
        break;
      case options.OPTION_PARTNER_OFFER:
        selector = this.enablePartnerOfferToggleInput;
        break;
      case options.OPTION_BIRTH_DATE:
        selector = this.askForBirthDateToggleInput;
        break;
      case options.OPTION_EMAIL_REGISTRATION:
        selector = this.sendEmailAfterRegistrationToggleInput;
        break;
      case options.OPTION_CART_LOGIN:
        selector = this.redisplayCartAtLoginToggleInput;
        break;
      default:
        throw new Error(`${option} was not found`);
    }
    await page.check(selector(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveGeneralFormButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new CustomerSettings();
