require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Search engine page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class SearchEngines extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on search engine page
   */
  constructor() {
    super();

    this.pageTitle = 'Search Engines •';

    // Header selectors
    this.newSearchEngineLink = '#page-header-desc-configuration-add';

    // Form selectors
    this.gridForm = '#search_engine_grid_panel';
    this.gridTableHeaderTitle = `${this.gridForm} h3.card-header-title`;

    // Table selectors
    this.gridTable = '#search_engine_grid_table';

    // Sort selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterColumn = filterBy => `${this.filterRow} #search_engine_${filterBy}`;
    this.filterSearchButton = 'button.grid-search-button';
    this.filterResetButton = 'button.grid-reset-button';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = row => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumns = row => `${this.tableBodyRow(row)} td`;


    // Columns selectors
    this.tableBodyColumn = (row, column) => `${this.tableBodyColumns(row)}.column-${column}`;

    // Row actions selectors
    this.tableColumnActions = row => `${this.tableBodyColumns(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = row => `${this.tableColumnActions(row)} a.grid-edit-row-link`;
    this.tableColumnActionsToggleButton = row => `${this.tableColumnActions(row)} a.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = row => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = row => `${this.tableColumnActionsDropdownMenu(row)} a.grid-delete-row-link`;

    // Confirmation modal
    this.deleteModalButtonYes = '#search_engine-grid-confirm-modal button.btn-confirm-submit';

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridForm} .col-form-label`;
    this.paginationNextLink = `${this.gridForm} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridForm} [aria-label='Previous']`;

    // Bulk actions selectors
    this.bulkActionMenuButton = 'button.js-bulk-actions-btn';
    this.selectAllLink = '#search_engine_grid_bulk_action_select_all + i';
    this.bulkDeleteLink = '#search_engine_grid_bulk_action_delete_selection';
  }

  /* Header methods */
  /**
   * Go to new search engine page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewSearchEnginePage(page) {
    await this.clickAndWaitForNavigation(page, this.newSearchEngineLink);
  }

  /* Filter methods */

  /**
   * Get number of search engines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTableHeaderTitle);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }

    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of search engines
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter search engines
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, filterBy, value) {
    await this.setValue(page, this.filterColumn(filterBy), value.toString());
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  getTextColumn(page, row, columnName) {
    return this.getTextContent(page, this.tableBodyColumn(row, columnName));
  }

  /**
   * Get column content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name of the value to return
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    // Get text column from each row
    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /* Sort functions */
  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, sortBy, sortDirection = 'asc') {
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
   * Go to edit search engine page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditSearchEnginePage(page, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete search engine
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteSearchEngine(page, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.confirmDelete(page);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
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
   * @param number {number} Pagination limit number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
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

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page) {
    await Promise.all([
      page.$eval(this.selectAllLink, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionMenuButton}:not([disabled])`),
    ]);
  }

  /**
   * Delete Search engine by bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteSearchEngine(page) {
    // Select all rows
    await this.bulkSelectRows(page);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton);

    // Click on delete
    await page.click(this.bulkDeleteLink);

    // Click on confirm delete on modal
    await this.confirmDelete(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Click on confirm delete on modal
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async confirmDelete(page) {
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);
  }
}

module.exports = new SearchEngines();
