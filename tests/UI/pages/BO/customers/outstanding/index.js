require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Outstanding page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Outstanding extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on 03_outstanding page
   */
  constructor() {
    super();

    this.pageTitle = 'Outstanding • PrestaShop';
    this.gridTable = '#outstanding_grid_table';
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
  }

  /*
Methods
 */
  /**
   * Reset filter in outstanding
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Oustanding row in table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row) {
    return this.getNumberFromText(page, this.tableColumn(row, 'id_invoice'));
  }

  /**
   * Get text from Column
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Oustanding row in table
   * @returns {Promise<void>}
   */
  async viewOrder(page, columnName, row) {
    return this.waitForSelectorAndClick(page, this.tableColumn(row, 'actions'));
  }
}


module.exports = new Outstanding();
