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

    // Selectors
    this.gridTable = '.table.table-striped';

    // Merchandise return table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.orderReturnName = row => `${this.tableBodyRow(row)} td:nth-child(2) a`;
  }

  /*
  Methods
   */
  /**
   * Get order return file name
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  getOrderReturnFileName(page, row = 1) {
    return this.getTextContent(page, this.orderReturnName(row));
  }
}

module.exports = new MerchandiseReturns();
