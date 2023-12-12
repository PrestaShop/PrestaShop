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
  private readonly orderEditToggle: (toEnable: string) => string;

  private readonly saveModuleForm: string;

  private readonly newOrderToggle: (toEnable: string) => string;

  private readonly submitCustomerNotifications: string;

  private readonly newOrderCheckbox: (toEnable: boolean) => string;

  private readonly addOrderEmailInput: string;

  private readonly returnEmailInput: string;

  private readonly outOfStockCheckbox: (toEnable: boolean) => string;

  private readonly returnsCheckbox: (toEnable: boolean) => string;

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
    this.newOrderCheckbox = (toEnable: boolean) => `#MA_MERCHANT_ORDER_${toEnable ? 'on' : 'off'}`;
    this.addOrderEmailInput = '#fieldset_1_1 div.form-wrapper div:nth-child(2) > div > div > input';
    this.outOfStockCheckbox = (toEnable: boolean) => `#MA_MERCHANT_OOS_${toEnable ? 'on' : 'off'}`;
    this.returnsCheckbox = (toEnable: boolean) => `#MA_RETURN_SLIP_${toEnable ? 'on' : 'off'}`;
    this.orderEditToggle = (toEnable: string) => `#MA_ORDER_EDIT_${toEnable}`;
    this.saveModuleForm = '#module_form_submit_btn';
    this.newOrderToggle = (toEnable: string) => `#MA_MERCHANT_ORDER_${toEnable}`;
    this.outOfStockToggle = (toEnable: string) => `#MA_MERCHANT_OOS_${toEnable}`;
    this.returnsToggle = (toEnable: string) => `#MA_RETURN_SLIP_${toEnable}`;
    this.returnEmailInput = '#fieldset_1_1 div.form-wrapper div:nth-child(7) > div > div input';
    this.submitMerchantNotifications = 'button[name="submitMAMerchant"]';
  }

  /* Methods */

  /**
   * Set edit order
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable edit order
   * @returns {Promise<number>}
   */
  async setEditOrder(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.orderEditToggle(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForURL(page, this.saveModuleForm);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set new order
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable new order
   * @param email {string} Email to set
   * @returns {Promise<number>}
   */
  async setNewOrder(page: Page, toEnable: boolean, email: string = ''): Promise<string> {
    await this.setChecked(page, this.newOrderCheckbox(toEnable));
    if (toEnable) {
      await this.setValue(page, this.addOrderEmailInput, email);
      await page.keyboard.press('Enter');
    }
    // @todo https://github.com/PrestaShop/PrestaShop/issues/34784
    await this.setChecked(page, this.outOfStockCheckbox(false));
    await this.setChecked(page, this.returnsCheckbox(false));
    await this.clickAndWaitForURL(page, this.submitMerchantNotifications);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set returns
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable status
   * @param email {string} Email to set
   * @returns {Promise<number>}
   */
  async setReturns(page: Page, toEnable: boolean, email: string = ''): Promise<string> {
    // @todo https://github.com/PrestaShop/PrestaShop/issues/34784
    await this.setChecked(page, this.newOrderCheckbox(false));
    await this.setChecked(page, this.outOfStockCheckbox(false));
    //
    await this.setChecked(page, this.returnsCheckbox(toEnable));
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
