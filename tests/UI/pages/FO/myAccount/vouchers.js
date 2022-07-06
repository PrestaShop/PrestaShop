require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Vouchers page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Vouchers extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on vouchers page
   */
  constructor() {
    super();

    this.pageTitle = 'Discount';

    // Selectors
    this.vouchersTable = '#content table.table';
    this.vouchersTableBody = `${this.vouchersTable} tbody`;
    this.vouchersTableRows = `${this.vouchersTableBody} tr`;
    this.vouchersTableRow = row => `${this.vouchersTableRows}:nth-child(${row})`;
    this.tableColumnCode = row => `${this.vouchersTableRow(row)} th:nth-child(1)`;
    this.tableColumnDescription = row => `${this.vouchersTableRow(row)} td:nth-child(2)`;
    this.tableColumnQuantity = row => `${this.vouchersTableRow(row)} td:nth-child(3)`;
    this.tableColumnValue = row => `${this.vouchersTableRow(row)} td:nth-child(4)`;
    this.tableColumnMinimum = row => `${this.vouchersTableRow(row)} td:nth-child(5)`;
    this.tableColumnCumulative = row => `${this.vouchersTableRow(row)} td:nth-child(6)`;
    this.tableColumnExpirationDate = row => `${this.vouchersTableRow(row)} td:nth-child(7)`;
  }

  /*
  Methods
   */

  /**
   * Get text column from table vouchers
   * @param page {Page} Browser tab
   * @param row {number} Row number in vouchers table
   * @param columnName {string} Column name in vouchers table
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableVouchers(page, row, columnName) {
    let columnSelector;

    switch (columnName) {
      case 'code':
        columnSelector = this.tableColumnCode(row);
        break;

      case 'description':
        columnSelector = this.tableColumnDescription(row);
        break;

      case 'quantity':
        columnSelector = this.tableColumnQuantity(row);
        break;

      case 'value':
        columnSelector = this.tableColumnValue(row);
        break;

      case 'minimum':
        columnSelector = this.tableColumnMinimum(row);
        break;

      case 'cumulative':
        columnSelector = this.tableColumnCumulative(row);
        break;

      case 'expiration_date':
        columnSelector = this.tableColumnExpirationDate(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }
}

module.exports = new Vouchers();
