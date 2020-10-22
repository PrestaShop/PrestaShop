require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewSQLQuery extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'SQL Manager';

    // Selectors
    this.sqlQueryResultTitle = '#main-div  div.card-header h3';
    this.resultsTable = '#main-div .table';
    this.tableColumnName = id => `${this.resultsTable} th:nth-child(${id})`;
    this.tableBody = `${this.resultsTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td:nth-child(${column})`;
  }

  /*
  Methods
   */
  /**
   * Get SQL query result number
   * @param page
   * @returns {Promise<number>}
   */
  getSQLQueryResultNumber(page) {
    return this.getNumberFromText(page, this.sqlQueryResultTitle);
  }

  /**
   * Get columns name
   * @param page
   * @param id
   * @returns {Promise<string>}
   */
  getColumnName(page, id = 1) {
    return this.getTextContent(page, this.tableColumnName(id));
  }

  /**
   * Get text from column in results table
   * @param page
   * @param row
   * @param column
   * @return {Promise<string>}
   */
  getTextColumn(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }
}

module.exports = new ViewSQLQuery();
