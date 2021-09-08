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
    this.vouchersTableCodeColumn = row => `${this.vouchersTableRow(row)} th`;
  }

  /*
  Methods
   */

  /**
   * Get voucher code from table
   * @param page {Page} Browser tab
   * @param row {number} Row number in vouchers table
   * @returns {Promise<string>}
   */
  getVoucherCodeFromTable(page, row) {
    return this.getTextContent(page, this.vouchersTableCodeColumn(row));
  }
}

module.exports = new Vouchers();
