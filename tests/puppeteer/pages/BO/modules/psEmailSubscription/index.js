require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class PsEmailSubscription extends BOBasePage {
  constructor(page) {
    super(page);

    // Header selectors
    this.pageHeadSubtitle = 'h4.page-subtitle';

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
   * @override
   * Get module name from page title
   * @return {Promise<string>}
   */
  getPageTitle() {
    return this.getTextContent(this.pageHeadSubtitle);
  }

  /**
   * Get number of newsletter registrations
   * @return {Promise<int>}
   */
  async getNumberOfNewsletterRegistration() {
    if (await this.elementVisible(this.newsletterTableEmptyColumn, 1000)) {
      return 0;
    }
    return (await this.page.$$(this.newsletterTableRows)).length;
  }

  /**
   * Get list of emails registered to newsletter
   * @return {Promise<[]>}
   */
  async getListOfNewsletterRegistrationEmails() {
    const emails = [];
    const numberOfEmails = await this.getNumberOfNewsletterRegistration();

    // Get email from each row
    for (let row = 1; row <= numberOfEmails; row++) {
      await emails.push(await this.getTextContent(this.newsletterTableEmailColumn(row)));
    }

    return emails;
  }
};
