require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Merchandise returns page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MerchandiseReturns extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on merchandise returns page
   */
  constructor() {
    super();

    this.pageTitle = 'Order follow';
    this.alertNoMerchandiseReturns = 'You have no merchandise return authorizations.';

    // Selectors
    this.alertInfoDiv = '#content div.alert-info';
    this.gridTable = '.table.table-striped';

    // Merchandise return table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableBodyRow(row)} td:nth-child(${column})`;
    this.orderReturnName = row => `${this.tableBodyRow(row)} td:nth-child(2) a`;
    this.orderReturnStatus = row => `${this.tableBodyRow(row)} td:nth-child(3)`;
  }

  /*
  Methods
   */

  /**
   * Get alert text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getAlertText(page) {
    return this.getTextContent(page, this.alertInfoDiv);
  }

  /**
   * Get text column from merchandise returns table
   * @param page {Page} Browser tab
   * @param columnName {number} Column name in table
   * @param row {number} Row number in table
   * @returns {Promise<string>}
   */
  getTextColumn(page, columnName, row = 1) {
    let columnSelector;

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
}

module.exports = new MerchandiseReturns();
