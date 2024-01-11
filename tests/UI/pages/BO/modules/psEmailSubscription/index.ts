import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_email_subscription, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsEmailSubscription extends ModuleConfiguration {
  public readonly pageTitle: string;

  public readonly updateSettingsSuccessMessage: string;

  private readonly sendVerificationEmail: (toEnable: string) => string;

  private readonly sendConfirmationEmail: (toEnable: string) => string;

  private readonly welcomeVoucherInput: string;

  private readonly saveSettingsForm: string;

  private readonly newsletterTable: string;

  private readonly newsletterTableBody: string;

  private readonly newsletterTableRows: string;

  private readonly newsletterTableRow: (row: number) => string;

  private readonly newsletterTableEmptyColumn: string;

  private readonly newsletterTableEmailColumn: (row: number) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();
    this.pageTitle = 'Newsletter subscription';
    this.updateSettingsSuccessMessage = 'Settings updated';

    // Selectors in settings block
    this.sendVerificationEmail = (toEnable: string) => `#NW_VERIFICATION_EMAIL_${toEnable}`;
    this.sendConfirmationEmail = (toEnable: string) => `label[for='NW_CONFIRMATION_EMAIL_${toEnable}']`;
    this.welcomeVoucherInput = '#NW_VOUCHER_CODE';
    this.saveSettingsForm = '#module_form_submit_btn';

    // Newsletter registrations table selectors
    this.newsletterTable = '#table-merged';
    this.newsletterTableBody = `${this.newsletterTable} tbody`;
    this.newsletterTableRows = `${this.newsletterTableBody} tr`;
    this.newsletterTableRow = (row: number) => `${this.newsletterTableRows}:nth-child(${row})`;
    this.newsletterTableEmptyColumn = `${this.newsletterTableRows} td.list-empty`;
    this.newsletterTableEmailColumn = (row: number) => `${this.newsletterTableRow(row)} td:nth-child(5)`;
  }

  /* Methods */
  /**
   * Set send verification email
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable send verification email
   * @returns {Promise<string>}
   */
  async setSendVerificationEmail(page: Page, toEnable: boolean): Promise<string> {
    await page.locator(this.sendVerificationEmail(toEnable ? 'on' : 'off')).click({force: true});
    await this.clickAndWaitForLoadState(page, this.saveSettingsForm);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set send confirmation email
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable send confirmation email
   * @returns {Promise<string>}
   */
  async setSendConfirmationEmail(page: Page, toEnable: boolean): Promise<string> {
    // @todo https://github.com/PrestaShop/PrestaShop/issues/35004
    if (toEnable && await this.elementVisible(page, this.sendConfirmationEmail('on'))) {
      await page.locator(this.sendConfirmationEmail(toEnable ? 'on' : 'off')).click({force: true});
    }
    await page.locator(this.sendConfirmationEmail(toEnable ? 'on' : 'off')).click({force: true});
    await this.clickAndWaitForLoadState(page, this.saveSettingsForm);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set welcome voucher
   * @param page {Page} Browser tab
   * @param voucher {string} Value of voucher to set in the input
   * @returns {Promise<string>}
   */
  async setWelcomeVoucher(page: Page, voucher: string): Promise<string> {
    await this.setValue(page, this.welcomeVoucherInput, voucher);
    await this.clickAndWaitForLoadState(page, this.saveSettingsForm);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get number of newsletter registrations
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewsletterRegistration(page: Page): Promise<number> {
    if (await this.elementVisible(page, this.newsletterTableEmptyColumn, 1000)) {
      return 0;
    }

    return page.locator(this.newsletterTableRows).count();
  }

  /**
   * Get list of emails registered to newsletter
   * @param page {Page} Browser tab
   * @return {Promise<Array<string>>}
   */
  async getListOfNewsletterRegistrationEmails(page: Page): Promise<string[]> {
    const emails: string[] = [];
    const numberOfEmails: number = await this.getNumberOfNewsletterRegistration(page);

    // Get email from each row
    for (let row = 1; row <= numberOfEmails; row++) {
      emails.push(await this.getTextContent(page, this.newsletterTableEmailColumn(row)));
    }

    return emails;
  }
}

export default new PsEmailSubscription();
