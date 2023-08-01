import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Administration page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AdministrationPage extends BOBasePage {
  public readonly pageTitle: string;

  private readonly cookiesIpAddressRadioButton: (status: string) => string;

  private readonly generalFormSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitle = `Administration • ${global.INSTALL.SHOP_NAME}`;
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // General form
    this.cookiesIpAddressRadioButton = (status: string) => `#general_check_ip_address_${status}`;
    this.generalFormSaveButton = '#configuration_fieldset_general button';
    this.alertSuccessBlock = 'div.alert.alert-success[role=alert] div.alert-text';
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
   */
  async setCookiesIPAddress(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.cookiesIpAddressRadioButton(toEnable ? '1' : '0'));
    await this.waitForSelectorAndClick(page, this.generalFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  async setLifetimeFOCookies(page: Page, value: number): Promise<string> {
    await this.setValue(page, '#general_front_cookie_lifetime', value);
    await this.waitForSelectorAndClick(page, this.generalFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  async setLifetimeBOCookies(page: Page, value: number): Promise<string> {
    await this.setValue(page, '#general_back_cookie_lifetime', value);
    await this.waitForSelectorAndClick(page, this.generalFormSaveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AdministrationPage();
