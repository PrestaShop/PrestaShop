import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_emailalerts, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsEmailAlerts extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly newOrderToggle: (toEnable: string) => string;

  private readonly outOfStockToggle: (toEnable: string) => string;

  private readonly returnsToggle: (toEnable: string) => string;

  private readonly returnEmailInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email alerts page
   */
  constructor() {
    super();
    this.pageTitle = 'Mail alerts';
    this.successfulUpdateMessage = 'Settings updated successfully';

    // Selectors
    this.newOrderToggle = (toEnable: string) => `#MA_MERCHANT_ORDER_${toEnable}`;
    this.outOfStockToggle = (toEnable: string) => `#MA_MERCHANT_OOS_${toEnable}`;
    this.returnsToggle = (toEnable: string) => `#MA_RETURN_SLIP_${toEnable}`;
    this.returnEmailInput = '#fieldset_1_1 div.form-wrapper div:nth-child(7) > div > div input';
    this.saveButton = '#module_form_submit_btn_1';
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
    await this.setChecked(page, this.newOrderToggle('off'));
    await this.setChecked(page, this.outOfStockToggle('off'));
    //
    await this.setChecked(page, this.returnsToggle(toEnable ? 'on' : 'off'));
    if (toEnable) {
      await this.setValue(page, this.returnEmailInput, email);
      await page.keyboard.press('Enter');
    }
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsEmailAlerts();
