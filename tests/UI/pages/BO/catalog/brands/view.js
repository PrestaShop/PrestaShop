require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * View brands page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ViewBrand extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on view brand page
   */
  constructor() {
    super();

    // Selectors
    this.contentDiv = 'div.content-div';
    this.addressesGrid = `${this.contentDiv} > div.row > div > div.row:nth-of-type(2)`;
    this.addressesGridHeader = `${this.addressesGrid} h3.card-header`;
    this.addressesTableBody = `${this.addressesGrid} .card-body table tbody`;
    this.addressesTableRow = row => `${this.addressesTableBody} tr:nth-of-type(${row})`;
    this.addressesTableColumn = (row, column) => `${this.addressesTableRow(row)} td:nth-of-type(${column})`;
  }

  /*
  Methods
   */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row in table to get text column
   * @param column {string} Column to get text content
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableAddresses(page, row, column) {
    return this.getTextContent(page, this.addressesTableColumn(row, column));
  }

  /**
   * Get number of addresses in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfAddressesInGrid(page) {
    return this.getNumberFromText(page, this.addressesGridHeader);
  }
}

module.exports = new ViewBrand();
