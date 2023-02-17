import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * View brands page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ViewBrand extends BOBasePage {
  private readonly contentDiv: string;

  private readonly addressesGrid: string;

  private readonly addressesGridHeader: string;

  private readonly addressesTableBody: string;

  private readonly addressesTableRow: (row: number) => string;

  private readonly addressesTableColumn: (row: number, column: number) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on view brand page
   */
  constructor() {
    super();

    // Selectors
    this.contentDiv = 'div.content-div';
    this.addressesGrid = 'div[data-role=addresses-card]';
    this.addressesGridHeader = `${this.addressesGrid} h3.card-header`;
    this.addressesTableBody = `${this.addressesGrid} .card-body table tbody`;
    this.addressesTableRow = (row: number) => `${this.addressesTableBody} tr:nth-of-type(${row})`;
    this.addressesTableColumn = (row: number, column: number) => `${this.addressesTableRow(row)} td:nth-of-type(${column})`;
  }

  /*
  Methods
   */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @param column {number} Column to get text content
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page: Page, row: number, column: number): Promise<string> {
    return this.getTextContent(page, this.addressesTableColumn(row, column));
  }

  /**
   * Get number of addresses in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfAddressesInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.addressesGridHeader);
  }
}

export default new ViewBrand();
