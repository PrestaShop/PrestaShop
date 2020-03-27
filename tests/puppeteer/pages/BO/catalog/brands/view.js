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
    this.addressesTableRow = `${this.addressesTableBody} tr:nth-of-type(%ROW)`;
    this.addressesTableColumn = `${this.addressesTableRow} td:nth-of-type(%COLUMN)`;
    this.productsGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(3)`;
    this.productsGridHeader = `${this.productsGrid} h3.card-header`;
  }

  /*
  Methods
   */
  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextContent(
      this.addressesTableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * get number of addresses in grid
   * @return {Promise<integer>}
   */
  async getNumberOfAddressesInGrid() {
    return this.getNumberFromText(this.addressesGridHeader);
  }
};
