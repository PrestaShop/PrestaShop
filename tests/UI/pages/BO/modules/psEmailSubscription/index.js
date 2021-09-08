require('module-alias/register');
const ModuleConfiguration = require('@pages/BO/modules/moduleConfiguration');

/**
 * Module configuration page for module : ps_email_subscription, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsEmailSubscription extends ModuleConfiguration.constructor {
  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    // Newsletter registrations table selectors
    this.newsletterTable = '#table-merged';
    this.newsletterTableBody = `${this.newsletterTable} tbody`;
    this.newsletterTableRows = `${this.newsletterTableBody} tr`;
    this.newsletterTableRow = row => `${this.newsletterTableRows}:nth-child(${row})`;
    this.newsletterTableEmptyColumn = `${this.newsletterTableRows} td.list-empty`;
    this.newsletterTableEmailColumn = row => `${this.newsletterTableRow(row)} td:nth-child(5)`;
  }

  /* Methods */

  /**
   * Get number of newsletter registrations
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewsletterRegistration(page) {
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
  async getListOfNewsletterRegistrationEmails(page) {
    const emails = [];
    const numberOfEmails = await this.getNumberOfNewsletterRegistration(page);

    // Get email from each row
    for (let row = 1; row <= numberOfEmails; row++) {
      emails.push(await this.getTextContent(page, this.newsletterTableEmailColumn(row)));
    }

    return emails;
  }
}

module.exports = new PsEmailSubscription();
