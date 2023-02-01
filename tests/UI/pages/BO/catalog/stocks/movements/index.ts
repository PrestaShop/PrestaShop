import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Movements page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Movements extends BOBasePage {
  public readonly pageTitle: string;

  private readonly stocksNavItemLink: string;

  private readonly searchForm: string;

  private readonly searchInput: string;

  private readonly searchButton: string;

  private readonly gridTable: string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableProductNameColumn: (row: number) => string;

  private readonly tableProductReferenceColumn: (row: number) => string;

  private readonly tableQuantityColumn: (row: number) => string;

  private readonly productListLoading: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on movements page
   */
  constructor() {
    super();

    this.pageTitle = 'Stock •';

    // Header selectors
    this.stocksNavItemLink = '#head_tabs li:nth-child(1) > a';

    // Simple filter selectors
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} .search-input input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;

    // Table selectors
    this.gridTable = '.stock-movements table.table';
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = (row: number) => `${this.tableRows}:nth-child(${row})`;
    this.tableProductNameColumn = (row: number) => `${this.tableRow(row)} td:nth-child(2) div.media-body p`;
    this.tableProductReferenceColumn = (row: number) => `${this.tableRow(row)} td:nth-child(3)`;
    this.tableQuantityColumn = (row: number) => `${this.tableRow(row)} td:nth-child(5) span.qty-number`;

    // Loader
    this.productListLoading = `${this.tableRow(1)} td:nth-child(1) div.ps-loader`;
  }

  /* Header methods */
  /**
   * Go to stocks page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabStocks(page: Page): Promise<void> {
    await page.click(this.stocksNavItemLink);
    await this.waitForVisibleSelector(page, `${this.stocksNavItemLink}.active`);
  }

  /**
   * Filter by a word
   * @param page {Page} Browser tab
   * @param value {string} Value to set on filter input
   * @returns {Promise<void>}
   */
  async simpleFilter(page: Page, value: string): Promise<void> {
    await page.type(this.searchInput, value);
    await Promise.all([
      page.click(this.searchButton),
      this.waitForVisibleSelector(page, this.productListLoading),
    ]);
    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /* Table methods */
  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.tableProductNameColumn(row));
      case 'reference':
        return this.getTextContent(page, this.tableProductReferenceColumn(row));
      case 'quantity':
        return (await this.getTextContent(page, this.tableQuantityColumn(row))).replace(' ', '');
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get number of element in movements grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return (await page.$$(this.tableRows)).length;
  }
}

export default new Movements();
