import BOBasePage from '@pages/BO/BObasePage';
import CustomerSettingsOptions from '@pages/BO/shopParameters/customerSettings/options';

import type {Page} from 'playwright';

/**
 * Customer settings page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CustomerSettings extends BOBasePage {
  public readonly pageTitle: string;

  private readonly titlesSubtab: string;

  private readonly groupsSubtab: string;

  private readonly redisplayCartAtLoginToggleInput: (toggle: number) => string;

  private readonly enablePartnerOfferToggleInput: (toggle: number) => string;

  private readonly sendEmailAfterRegistrationToggleInput: (toggle: number) => string;

  private readonly askForBirthDateToggleInput: (toggle: number) => string;

  private readonly enableB2BModeToggle: (toggle: number) => string;

  private readonly saveGeneralFormButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on customer settings page
   */
  constructor() {
    super();

    this.pageTitle = `Customer settings • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';

    // Header selectors
    this.titlesSubtab = '#subtab-AdminGenders';
    this.groupsSubtab = '#subtab-AdminGroups';

    // Form selectors
    this.redisplayCartAtLoginToggleInput = (toggle: number) => `#form_redisplay_cart_at_login_${toggle}`;
    this.enablePartnerOfferToggleInput = (toggle: number) => `#form_enable_offers_${toggle}`;
    this.sendEmailAfterRegistrationToggleInput = (toggle: number) => `#form_send_email_after_registration_${toggle}`;
    this.askForBirthDateToggleInput = (toggle: number) => `#form_ask_for_birthday_${toggle}`;
    this.enableB2BModeToggle = (toggle: number) => `#form_enable_b2b_mode_${toggle}`;
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
  async goToTitlesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.titlesSubtab);
  }

  /**
   * Click on tab groups
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToGroupsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.groupsSubtab);
  }

  /**
   * Set option status
   * @param page {Page} Browser tab
   * @param option {string} Option to enable or disable
   * @param toEnable {boolean} True if we need to enable status
   * @return {Promise<string>}
   */
  async setOptionStatus(page: Page, option: string, toEnable: boolean = true): Promise<string> {
    let selector;

    switch (option) {
      case CustomerSettingsOptions.OPTION_B2B:
        selector = this.enableB2BModeToggle;
        break;
      case CustomerSettingsOptions.OPTION_PARTNER_OFFER:
        selector = this.enablePartnerOfferToggleInput;
        break;
      case CustomerSettingsOptions.OPTION_BIRTH_DATE:
        selector = this.askForBirthDateToggleInput;
        break;
      case CustomerSettingsOptions.OPTION_EMAIL_REGISTRATION:
        selector = this.sendEmailAfterRegistrationToggleInput;
        break;
      case CustomerSettingsOptions.OPTION_CART_LOGIN:
        selector = this.redisplayCartAtLoginToggleInput;
        break;
      default:
        throw new Error(`${option} was not found`);
    }
    await this.setChecked(page, selector(toEnable ? 1 : 0));
    await page.locator(this.saveGeneralFormButton).click();
    await this.elementNotVisible(page, selector(!toEnable ? 1 : 0));

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new CustomerSettings();
