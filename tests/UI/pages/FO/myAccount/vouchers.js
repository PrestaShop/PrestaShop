require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Vouchers extends FOBasePage {
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
   * Get
   * @param page {Page} Browser tab
   * @param row {number} Row number in vouchers table
   * @returns {string}
   */
  getVoucherCodeFromTable(page, row) {
    return this.getTextContent(page, this.vouchersTableCodeColumn(row));
  }
}

module.exports = new Vouchers();
