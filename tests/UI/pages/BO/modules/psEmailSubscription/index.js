require('module-alias/register');
const ModuleConfiguration = require('@pages/BO/modules/moduleConfiguration');

class PsEmailSubscription extends ModuleConfiguration.constructor {
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
   * @return {Promise<[]>}
   */
  async getListOfNewsletterRegistrationEmails(page) {
    const emails = [];
    const numberOfEmails = await this.getNumberOfNewsletterRegistration(page);

    // Get email from each row
    for (let row = 1; row <= numberOfEmails; row++) {
      await emails.push(await this.getTextContent(page, this.newsletterTableEmailColumn(row)));
    }

    return emails;
  }
}

module.exports = new PsEmailSubscription();
