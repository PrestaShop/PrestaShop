require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View sql query page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class ViewSQLQuery extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on view sql query page
   */
  constructor() {
    super();

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryResultTitle = '#card-title';
    this.resultsTable = '#grid-table';
    this.tableColumnName = column => `${this.resultsTable} th:nth-child(${column})`;
    this.tableBody = `${this.resultsTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.grid-${column}-value`;
  }

  /*
  Methods
   */
  /**
   * Get SQL query result number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getSQLQueryResultNumber(page) {
    return this.getNumberFromText(page, this.sqlQueryResultTitle);
  }

  /**
   * Get columns name
   * @param page {Page} Browser tab
   * @param column {number} Id of column to get column name
   * @returns {Promise<string>}
   */
  getColumnName(page, column = 1) {
    return this.getTextContent(page, this.tableColumnName(column));
  }

  /**
   * Get text from column in results table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Name of the column
   * @return {Promise<string>}
   */
  getTextColumn(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }
}

module.exports = new ViewSQLQuery();
