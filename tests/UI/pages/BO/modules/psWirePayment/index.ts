import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import {type Page} from 'playwright';

/**
 * Module configuration page for module : ps_wirepayment, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsWirePaymentPage extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly accountDetailsForm: string;

  private readonly accountOwnerInput: string;

  private readonly accountDetailsInput: string;

  private readonly bankAddresInput: string;

  private readonly submitAccountDetails: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on page
   */
  constructor() {
    super();
    this.pageTitle = 'Bank transfer';
    this.successfulUpdateMessage = 'Settings updated';

    // Selectors
    // Customer Notifications
    this.accountDetailsForm = '#module_form';
    this.accountOwnerInput = `${this.accountDetailsForm} #BANK_WIRE_OWNER`;
    this.accountDetailsInput = `${this.accountDetailsForm} #BANK_WIRE_DETAILS`;
    this.bankAddresInput = `${this.accountDetailsForm} #BANK_WIRE_ADDRESS`;
    this.submitAccountDetails = `${this.accountDetailsForm} button#module_form_submit_btn`;
  }

  /* Methods */

  /**
   * Define the field "Account Owner"
   * @param page {Page} Browser tab
   * @param accountOwner {string}
   * @returns {Promise<void>}
   */
  async setAccountOwner(page: Page, accountOwner: string): Promise<void> {
    return this.setInputValue(page, this.accountOwnerInput, accountOwner);
  }

  /**
   * Define the field "Account Details"
   * @param page {Page} Browser tab
   * @param accountDetails {string}
   * @returns {Promise<void>}
   */
  async setAccountDetails(page: Page, accountDetails: string): Promise<void> {
    return this.setInputValue(page, this.accountDetailsInput, accountDetails);
  }

  /**
   * Define the field "Bank Address"
   * @param page {Page} Browser tab
   * @param bankAddress {string}
   * @returns {Promise<void>}
   */
  async setBankAddress(page: Page, bankAddress: string): Promise<void> {
    return this.setInputValue(page, this.bankAddresInput, bankAddress);
  }

  /**
   * Save the "Contact details" form
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveFormContactDetails(page: Page): Promise<string> {
    await page.locator(this.submitAccountDetails).click();

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsWirePaymentPage();
