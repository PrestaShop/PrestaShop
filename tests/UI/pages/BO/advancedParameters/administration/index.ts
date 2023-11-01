import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Administration page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AdministrationPage extends BOBasePage {
  public readonly pageTitle: string;

  public readonly dangerAlertCookieSameSite: string;

  private readonly cookiesIpAddressRadioButton: (status: string) => string;

  private readonly lifeTimeFOCookies: string;

  private readonly lifeTimeBOCookies: string;

  private readonly generalCookieSameSite: string;

  private readonly generalFormSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitle = `Administration • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';
    this.dangerAlertCookieSameSite = 'The SameSite=None attribute is only available in secure mode.';

    // Selectors
    // General form
    this.cookiesIpAddressRadioButton = (status: string) => `#general_check_ip_address_${status}`;
    this.lifeTimeFOCookies = '#general_front_cookie_lifetime';
    this.lifeTimeBOCookies = '#general_back_cookie_lifetime';
    this.generalCookieSameSite = '#general_cookie_samesite';
    this.generalFormSaveButton = '#configuration_fieldset_general button';
    this.alertSuccessBlock = 'div.alert[role=alert] div.alert-text';
  }

  /*
  Methods
   */
  /**
   * Is check cookies enabled
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCheckCookiesAddressEnabled(page: Page): Promise<boolean> {
    return this.isChecked(page, `${this.cookiesIpAddressRadioButton('1')}`);
  }

  /**
   * Set cookies ip address
   * @param page {Page} Browser tab
   * @param toEnable {Promise<string>}
   * @return {Promise<void>}
   */
  async setCookiesIPAddress(page: Page, toEnable: boolean = true): Promise<void> {
    await this.setChecked(page, this.cookiesIpAddressRadioButton(toEnable ? '1' : '0'));
  }

  /**
   * Sel lifeTime FO cookies
   * @param page {Page} Browser tab
   * @param value {number} Number to set on lifetime FO cookies
   * @return {Promise<void>}
   */
  async setLifetimeFOCookies(page: Page, value: number): Promise<void> {
    await this.setValue(page, this.lifeTimeFOCookies, value);
  }

  /**
   * Sel lifeTime BO cookies
   * @param page {Page} Browser tab
   * @param value {number} Number to set on lifetime BO cookies
   * @return {Promise<void>}
   */
  async setLifetimeBOCookies(page: Page, value: number): Promise<void> {
    await this.setValue(page, this.lifeTimeBOCookies, value);
  }

  /**
   * Select cookie same site
   * @param page {Page} Browser tab
   * @param value {sting} Value to set on cookie same site
   * @return {Promise<void>}
   */
  async setCookieSameSite(page: Page, value: string): Promise<void> {
    await this.selectByVisibleText(page, this.generalCookieSameSite, value);
  }

  /**
   * Save general form
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveGeneralForm(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.generalFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AdministrationPage();
