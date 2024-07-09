import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import {type Page} from 'playwright';

/**
 * Module configuration page for module : ps_checkpayment, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsCheckPaymentPage extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly configurationForm: string;

  private readonly payeeInput: string;

  private readonly addressInput: string;

  private readonly submitContactDetails: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on page
   */
  constructor() {
    super();
    this.pageTitle = 'Payments by check';
    this.successfulUpdateMessage = 'Settings updated';

    // Selectors
    // Customer Notifications
    this.configurationForm = '#configuration_form';
    this.payeeInput = `${this.configurationForm} #CHEQUE_NAME`;
    this.addressInput = `${this.configurationForm} #CHEQUE_ADDRESS`;
    this.submitContactDetails = `${this.configurationForm} button[name="btnSubmit"]`;
  }

  /* Methods */

  /**
   * Define the field "Payee"
   * @param page {Page} Browser tab
   * @param payee {string}
   * @returns {Promise<void>}
   */
  async setPayee(page: Page, payee: string): Promise<void> {
    return this.setInputValue(page, this.payeeInput, payee);
  }

  /**
   * Define the field "Address"
   * @param page {Page} Browser tab
   * @param address {string}
   * @returns {Promise<void>}
   */
  async setAddress(page: Page, address: string): Promise<void> {
    return this.setInputValue(page, this.addressInput, address);
  }

  /**
   * Save the "Contact details" form
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveFormContactDetails(page: Page): Promise<string> {
    await page.locator(this.submitContactDetails).click();

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsCheckPaymentPage();
