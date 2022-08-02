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

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

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
   * @param row {number} Order row in table
   * @returns {Promise<string|number>}
   */
  async getTextColumn(page, columnName, row) {
    if (columnName === 'id_invoice') {
      return this.getNumberFromText(page, this.tableColumn(row, 'id_invoice'));
    }

    return this.getTextContent(page, this.tableColumn(row, columnName));
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

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @returns {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Get number of orders in page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOutstandingInPage(page) {
    return (await page.$$(`${this.tableBody} tr`)).length;
  }

  /**
   * Get order total price
   * @param page {Page} Browser tab
   * @param row {number} Order row on table
   * @returns {Promise<number>}
   */
  async getOutstandingAllowancePrice(page, row) {
    // Delete the first character (currency symbol) before getting price ATI
    return parseFloat((await this.getTextColumn(page, 'outstanding_allow_amount', row)).substring(1));
  }

  /**
   * Get column content in all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name on table
   * @returns {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    let rowContent;
    const rowsNumber = await this.getNumberOfOutstandingInPage(page);
    const allRowsContentTable = [];
    for (let i = 1; i <= rowsNumber; i++) {
      if (column === 'outstanding_allow_amount') {
        rowContent = await this.getOutstandingAllowancePrice(page, i);
      } else {
        rowContent = await this.getTextColumn(page, column, i);
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }
}

module.exports = new Outstanding();
