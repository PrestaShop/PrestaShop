import FOBasePage from '@pages/FO/FObasePage';

import {MerchandiseReturns as OrderMerchandiseReturns} from '@data/types/order';

import type {Page} from 'playwright';

/**
 * Merchandise returns page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MerchandiseReturns extends FOBasePage {
  public readonly pageTitle: string;

  public readonly alertNoMerchandiseReturns: string;

  private readonly alertInfoDiv: string;

  private readonly gridTable: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableColumn: (row: number, column: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on merchandise returns page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Order follow';
    this.alertNoMerchandiseReturns = 'You have no merchandise return authorizations.';

    // Selectors
    this.alertInfoDiv = '#content div.alert-info';
    this.gridTable = '.table.table-striped';

    // Merchandise return table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableColumn = (row: number, column: number) => `${this.tableBodyRow(row)} td:nth-child(${column})`;
  }

  /*
  Methods
   */
  /**
   * Get alert text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getAlertText(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertInfoDiv);
  }

  /**
   * Get text column from merchandise returns table
   * @param page {Page} Browser tab
   * @param columnName {string} Column name in table
   * @param row {number} Row number in table
   * @returns {Promise<string>}
   */
  getTextColumn(page: Page, columnName: string, row: number = 1): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'orderReference':
        columnSelector = this.tableColumn(row, 1);
        break;

      case 'fileName':
        columnSelector = this.tableColumn(row, 2);
        break;

      case 'status':
        columnSelector = this.tableColumn(row, 3);
        break;

      case 'dateIssued':
        columnSelector = this.tableColumn(row, 4);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get merchandise returns details
   * @param page {Page} Browser tab
   * @param row {number} Row number in table
   */
  async getMerchandiseReturnsDetails(page: Page, row: number = 1): Promise<OrderMerchandiseReturns> {
    return {
      orderReference: await this.getTextContent(page, this.tableColumn(row, 1)),
      fileName: await this.getTextContent(page, this.tableColumn(row, 2)),
      status: await this.getTextContent(page, this.tableColumn(row, 3)),
      dateIssued: await this.getTextContent(page, this.tableColumn(row, 4)),
    };
  }

  /**
   * Go to return details page
   * @param page {Page} Browser tab
   * @param row {number} Row number in table
   * @returns {Promise<void>}
   */
  async goToReturnDetailsPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, `${this.tableColumn(row, 2)} a`);
  }

  /**
   * Download return form
   * @param page {Page} Browser tab
   * @param row {number} Row number in table
   * @returns {Promise<string|null>}
   */
  async downloadReturnForm(page: Page, row: number = 1): Promise<string | null> {
    return this.clickAndWaitForDownload(page, this.tableColumn(row, 5));
  }
}

const merchandiseReturnsPage = new MerchandiseReturns();
export {merchandiseReturnsPage, MerchandiseReturns};
