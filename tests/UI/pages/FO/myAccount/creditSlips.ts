import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Credit slip page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CreditSlip extends FOBasePage {
  public readonly pageTitle: string;

  public readonly noCreditSlipsInfoMessage: string;

  private readonly creditSlipsTable: string;

  private readonly creditSlipsTableRows: string;

  private readonly creditSlipsTableRow: (row: number) => string;

  private readonly creditSlipsTableColumn: (row: number, column: number) => string;

  private readonly backToYourAccountLink: string;

  private readonly homeLink: string;

  private readonly alertInfoBlock: string;

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
    this.creditSlipsTableRow = (row: number) => `${this.creditSlipsTableRows}:nth-child(${row})`;
    this.creditSlipsTableColumn = (row: number, column: number) => `${this.creditSlipsTableRow(row)} td:nth-child(${column})`;
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
  async getNumberOfCreditSlips(page: Page): Promise<number> {
    return (await page.$$(this.creditSlipsTableRows)).length;
  }

  /**
   * Get order reference from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getOrderReference(page: Page, creditSlipRow: number = 1): Promise<string> {
    return this.getTextContent(page, `${this.creditSlipsTableColumn(creditSlipRow, 1)} a`);
  }

  /**
   * Get credit slip ID from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getCreditSlipID(page: Page, creditSlipRow: number = 1): Promise<string> {
    return this.getTextContent(page, this.creditSlipsTableColumn(creditSlipRow, 2));
  }

  /**
   * Get date issue from credit slips page
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @return {Promise<string>}
   */
  getDateIssued(page: Page, creditSlipRow: number = 1): Promise<string> {
    return this.getTextContent(page, this.creditSlipsTableColumn(creditSlipRow, 3));
  }

  /**
   * Click on the order reference link in the order detail
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @returns {Promise<void>}
   */
  async clickOrderReference(page: Page, creditSlipRow: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, `${this.creditSlipsTableColumn(creditSlipRow, 1)} a`);
  }

  /**
   * Export data to PDF
   * @param page {Page} Browser tab
   * @param creditSlipRow {number} Row number in credit slips table
   * @returns {Promise<string|null>}
   */
  async downloadCreditSlip(page: Page, creditSlipRow: number = 1): Promise<string | null> {
    return this.clickAndWaitForDownload(page, `${this.creditSlipsTableColumn(creditSlipRow, 4)} a`);
  }

  /**
   * Get alert info message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertInfoMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertInfoBlock);
  }

  /**
   * Click on the "Back to your account" link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickBackToYourAccountLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.backToYourAccountLink);
  }

  /**
   * Click on the "Home" link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickHomeLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.homeLink);
  }
}

export default new CreditSlip();
