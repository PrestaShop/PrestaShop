import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * View sql query page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewSQLQuery extends BOBasePage {
  public readonly pageTitle: string;

  private readonly sqlQueryResultTitle: string;

  private readonly resultsTable: string;

  private readonly tableColumnName: (column: number) => string;

  private readonly tableBody: string;

  private readonly tableRows: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableColumn: (row: number, column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view sql query page
   */
  constructor() {
    super();

    this.pageTitle = `Result of SQL query • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.sqlQueryResultTitle = '.card-header';
    this.resultsTable = '#grid-table';
    this.tableColumnName = (column: number) => `${this.resultsTable} th:nth-child(${column})`;
    this.tableBody = `${this.resultsTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = (row: number) => `${this.tableRows}:nth-child(${row})`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.grid-${column}-value`;
  }

  /*
  Methods
   */
  /**
   * Get SQL query result number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getSQLQueryResultNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.sqlQueryResultTitle);
  }

  /**
   * Get columns name
   * @param page {Page} Browser tab
   * @param column {number} Id of column to get column name
   * @returns {Promise<string>}
   */
  getColumnName(page: Page, column: number = 1): Promise<string> {
    return this.getTextContent(page, this.tableColumnName(column));
  }

  /**
   * Get text from column in results table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Name of the column
   * @return {Promise<string>}
   */
  getTextColumn(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }
}

export default new ViewSQLQuery();
