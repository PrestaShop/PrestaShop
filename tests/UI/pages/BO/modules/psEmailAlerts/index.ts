import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_emailalerts, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsEmailAlerts extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly productAvailabilityCheckbox: (toEnable: boolean) => string;

  private readonly submitCustomerNotifications: string;

  private readonly newOrderToggle: (toEnable: boolean) => string;

  private readonly returnEmailInput: string;

  private readonly outOfStockToggle: (toEnable: boolean) => string;

  private readonly returnsToggle: (toEnable: boolean) => string;

  private readonly submitMerchantNotifications: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email alerts page
   */
  constructor() {
    super();
    this.pageTitle = 'Mail alerts';
    this.successfulUpdateMessage = 'Settings updated successfully';

    // Selectors
    // Customer Notifications
    this.productAvailabilityCheckbox = (toEnable: boolean) => `#MA_CUSTOMER_QTY_${toEnable ? 'on' : 'off'}`;
    this.submitCustomerNotifications = 'button[name="submitMailAlert"]';
    // Merchant Notifications
    this.newOrderToggle = (toEnable: boolean) => `#MA_MERCHANT_ORDER_${toEnable ? 'on' : 'off'}`;
    this.returnEmailInput = '#fieldset_1_1 div.form-wrapper div:nth-child(7) > div > div input';
    this.outOfStockToggle = (toEnable: boolean) => `#MA_MERCHANT_OOS_${toEnable ? 'on' : 'off'}`;
    this.returnsToggle = (toEnable: boolean) => `#MA_RETURN_SLIP_${toEnable ? 'on' : 'off'}`;
    this.submitMerchantNotifications = 'button[name="submitMAMerchant"]';
  }

  /* Methods */
  /**
   * Set returns
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable status
   * @param email {string} Email to set
   * @returns {Promise<number>}
   */

  async setReturns(page: Page, toEnable: boolean, email: string = ''): Promise<string> {
    // To delete after the fix of https://github.com/PrestaShop/PrestaShop/issues/34784
    await this.setChecked(page, this.newOrderToggle(false));
    await this.setChecked(page, this.outOfStockToggle(false));
    //
    await this.setChecked(page, this.returnsToggle(toEnable));
    if (toEnable) {
      await this.setValue(page, this.returnEmailInput, email);
      await page.keyboard.press('Enter');
    }
    await this.clickAndWaitForURL(page, this.submitMerchantNotifications);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Enable/Disable the "Product availability"
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable
   * @returns {Promise<void>}
   */
  async setProductAvailabilityStatus(page: Page, toEnable: boolean): Promise<void> {
    return this.setChecked(page, this.productAvailabilityCheckbox(toEnable), true);
  }

  /**
   * Return the "Product availability" status
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async getProductAvailabilityStatus(page: Page): Promise<boolean> {
    return this.isChecked(page, this.productAvailabilityCheckbox(true));
  }

  /**
   * Save the "Customer Notifications" form
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveFormCustomerNotifications(page: Page): Promise<string> {
    await page.locator(this.submitCustomerNotifications).click();

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsEmailAlerts();
