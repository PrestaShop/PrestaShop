import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_email_subscription, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsEmailSubscription extends ModuleConfiguration {
  public readonly pageTitle: string;

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
   * Get number of newsletter registrations
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewsletterRegistration(page: Page): Promise<number> {
    if (await this.elementVisible(page, this.newsletterTableEmptyColumn, 1000)) {
      return 0;
    }

    return (await page.$$(this.newsletterTableRows)).length;
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
