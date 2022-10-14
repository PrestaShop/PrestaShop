require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Credit slip page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CreditSlip extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on credit slip page
   */
  constructor() {
    super();

    // Title
    this.pageTitle = 'Credit slip';

    // Message
    this.noCreditSlipsInfoMessage = 'You have not received any credit slips.';

    // Selectors
    this.creditSlipsTable = '#content table';
    this.creditSlipsTableRows = `${this.creditSlipsTable} tbody tr`;
    this.creditSlipsTableRow = row => `${this.creditSlipsTableRows}:nth-child(${row})`;
    this.creditSlipsTableColumn = (row, column) => `${this.creditSlipsTableRow(row)} td:nth-child(${column})`;
    this.backToYourAccountLink = 'a.account-link[data-role="back-to-your-account"]';
    this.homeLink = 'a.account-link[data-role="home"]';
    // Alert block selectors
    this.alertInfoBlock = '#content .alert.alert-info';
  }

  /*
  Methods
   */

  /**
   * Get number of credit slips in credit slips page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfCreditSlips(page) {
    return (await page.$$(this.creditSlipsTableRows)).length;
  }

  /**
   * Get order reference from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getOrderReference(page, creditSlipRow = 1) {
    return this.getTextContent(page, `${this.creditSlipsTableColumn(creditSlipRow, 1)} a`);
  }

  /**
   * Get credit slip ID from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getCreditSlipID(page, creditSlipRow = 1) {
    return this.getTextContent(page, this.creditSlipsTableColumn(creditSlipRow, 2));
  }

  /**
   * Get date issue from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getDateIssued(page, creditSlipRow = 1) {
    return this.getTextContent(page, this.creditSlipsTableColumn(creditSlipRow, 3));
  }

  /**
   * Click on the order reference link in the order detail
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @returns {Promise<void>}
   */
  async clickOrderReference(page, creditSlipRow = 1) {
    await this.clickAndWaitForNavigation(page, `${this.creditSlipsTableColumn(creditSlipRow, 1)} a`);
  }

  /**
   * Export data to PDF
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @returns {Promise<string>}
   */
  async downloadCreditSlip(page, creditSlipRow = 1) {
    return this.clickAndWaitForDownload(page, `${this.creditSlipsTableColumn(creditSlipRow, 4)} a`);
  }

  /**
   * Get alert info message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertInfoMessage(page) {
    return this.getTextContent(page, this.alertInfoBlock);
  }

  /**
   * Click on the "Back to your account" link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickBackToYourAccountLink(page) {
    await this.clickAndWaitForNavigation(page, this.backToYourAccountLink);
  }

  /**
   * Click on the "Home" link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickHomeLink(page) {
    await this.clickAndWaitForNavigation(page, this.homeLink);
  }
}

module.exports = new CreditSlip();
