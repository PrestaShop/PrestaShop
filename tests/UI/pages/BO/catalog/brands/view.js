require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ViewBrand extends BOBasePage {
  constructor() {
    super();

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
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextContent(page, this.addressesTableColumn(row, column));
  }

  /**
   * Get number of addresses in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfAddressesInGrid(page) {
    return this.getNumberFromText(page, this.addressesGridHeader);
  }
}

module.exports = new ViewBrand();
