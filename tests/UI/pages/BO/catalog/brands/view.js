require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class ViewBrand extends BOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.contentDiv = 'div.content-div';
    this.addressesGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(2)`;
    this.addressesGridHeader = `${this.addressesGrid} h3.card-header`;
    this.addressesTableBody = `${this.addressesGrid} .card-body table tbody`;
    this.addressesTableRow = row => `${this.addressesTableBody} tr:nth-of-type(${row})`;
    this.addressesTableColumn = (row, column) => `${this.addressesTableRow(row)} td:nth-of-type(${column})`;
    this.productsGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(3)`;
  }

  /*
  Methods
   */
  /**
   * Get text from a column
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextContent(this.addressesTableColumn(row, column));
  }

  /**
   * Get number of addresses in grid
   * @returns {Promise<number>}
   */
  async getNumberOfAddressesInGrid() {
    return this.getNumberFromText(this.addressesGridHeader);
  }
};
