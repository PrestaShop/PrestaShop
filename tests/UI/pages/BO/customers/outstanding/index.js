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
   * Setting up texts and selectors to use on outstanding page
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
    this.tableColumnActionType = (row, column) => `${this.tableColumn(row, column)} a`;

    this.cardHeaderTitle = '#outstanding_grid_panel .card-header-title';
    this.paginationLabel = '#outstanding_grid .col-form-label';
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationNextLink = '#pagination_next_url';
    this.paginationPreviousLink = '#outstanding_grid_panel .pagination .previous a.page-link';
  }

  /* Methods */
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
   * @param row {number} Outstanding row in table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row) {
    return this.getNumberFromText(page, this.tableColumn(row, 'id_invoice'));
  }

  /**
   * Click on view order
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Outstanding row in table
   * @returns {Promise<void>}
   */
  async viewOrder(page, columnName, row = 1) {
    await this.waitForSelectorAndClick(page, this.tableColumnActionType(row, columnName));
  }

  /**
   * Click on view invoice
   * @param page {Page} Browser tab
   * @param columnName {string} Column name on table
   * @param row {number} Outstanding row in table
   * @returns {Promise<string>}
   */
  async viewInvoice(page, columnName, row = 1) {
    return this.clickAndWaitForDownload(page, this.tableColumnActionType(row, 'invoice'));
  }

  /**
   * Get the number of outstanding
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOutstanding(page) {
    return this.getNumberFromText(page, this.cardHeaderTitle);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

}

module.exports = new Outstanding();
