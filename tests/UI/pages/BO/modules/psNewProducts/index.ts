import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_newproducts, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsNewProducts extends ModuleConfiguration {
  public readonly pageSubTitle: string;

  public readonly updateSettingsSuccessMessage: string;

  private readonly productsToDisplayInput: string;

  private readonly saveSettingsForm: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    this.pageSubTitle = 'New products block';
    this.updateSettingsSuccessMessage = 'The settings have been updated.';

    // Selectors
    this.productsToDisplayInput = '#NEW_PRODUCTS_NBR';
    this.saveSettingsForm = '#module_form_submit_btn';
  }

  /* Methods */
  /**
   * Set Products to display
   * @param page {Page} Browser tab
   * @param value {number}
   * @returns {Promise<string>}
   */
  async setNumProductsToDisplay(page: Page, value: number): Promise<string> {
    await page.locator(this.productsToDisplayInput).fill(value.toString());
    await this.clickAndWaitForLoadState(page, this.saveSettingsForm);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get Products to display
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getNumProductsToDisplay(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.productsToDisplayInput, 'value');
  }
}

export default new PsNewProducts();
