require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Logs page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Logs extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on logs page
   */
  constructor() {
    super();

    this.pageTitle = 'Logs •';

    // List of logs
    this.gridPanel = '#logs_grid_panel';
    this.gridTitle = `${this.gridPanel} h3.card-header-title`;
    this.listForm = '#logs_grid';
    this.listTableRow = row => `${this.listForm} tbody tr:nth-child(${row})`;
    this.listTableColumn = (row, column) => `${this.listTableRow(row)} td.column-${column}`;
    this.gridActionButton = '#logs-grid-actions-button';
    this.eraseAllButton = '#logs_grid_action_delete_all_email_logs';

    // Filters
    this.filterColumnInput = filterBy => `${this.listForm} #logs_${filterBy}`;
    this.filterSearchButton = `${this.listForm} .grid-search-button`;
    this.filterResetButton = `${this.listForm} .grid-reset-button`;

    // Sort Selectors
    this.tableHead = `${this.listForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter list of logs
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterLogs(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.filterColumnInput(filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row in table
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumn(page, row, column) {
    return this.getTextContent(page, this.listTableColumn(row, column));
  }

  /**
   * Erase all logs
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async eraseAllLogs(page) {
    // Add listener to dialog to accept erase
    this.dialogListener(page);

    await page.click(this.gridActionButton);
    await this.waitForSelectorAndClick(page, this.eraseAllButton);

    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextColumn(page, i, column);
      if (column === 'employee' && rowContent === 'N/A') {
        rowContent = '';
      }

      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Sort methods */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Pagination methods
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

  /**
   * Filter logs by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value of date from to set on filter input
   * @param dateTo {string} Value of date to to set on filter input
   * @returns {Promise<void>}
   */
  async filterLogsByDate(page, dateFrom, dateTo) {
    await page.type(this.filterColumnInput('date_add_from'), dateFrom);
    await page.type(this.filterColumnInput('date_add_to'), dateTo);
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }
}

module.exports = new Logs();
