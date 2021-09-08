require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Monitoring page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Monitoring extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on monitoring page
   */
  constructor() {
    super();

    this.pageTitle = 'Monitoring •';

    // Selectors
    this.gridPanel = table => `#${table}_grid_panel`;
    this.gridTable = table => `#${table}_grid_table`;
    this.gridHeaderTitle = table => `${this.gridPanel(table)} div.card-header h3`;

    // Filters
    this.filterColumn = (table, filterBY) => `${this.gridTable(table)} #${table}_${filterBY}`;
    this.filterSearchButton = table => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = table => `${this.gridTable(table)} .grid-reset-button`;

    // Table
    this.tableBody = table => `${this.gridTable(table)} tbody`;
    this.tableRow = (table, row) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableEmptyRow = table => `${this.tableBody(table)} tr.empty_row`;
    this.tableColumn = (table, row, column) => `${this.tableRow(table, row)} td.column-${column}`;

    // enable column
    this.enableColumn = (table, row) => this.tableColumn(table, row, 'active');
    this.enableColumnValidIcon = row => `${this.enableColumn(row)} i.grid-toggler-icon-valid`;

    // Actions buttons in Row
    this.actionsColumn = (table, row) => `${this.tableRow(table, row)} td.column-actions`;
    this.editRowLink = (table, row) => `${this.actionsColumn(table, row)} a.grid-edit-row-link`;
    this.dropdownToggleButton = (table, row) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table, row) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table, row) => `${this.dropdownToggleMenu(table, row)} a.grid-delete-row-link`;

    // Category selectors
    this.viewCategoryRowLink = row => `${this.actionsColumn('empty_category', row)} a.grid-view-row-link`;
    this.editCategoryRowLink = row => `${this.dropdownToggleMenu('empty_category', row)} a.grid-edit-row-link`;
    this.deleteCategoryRowLink = row => `${this.dropdownToggleMenu('empty_category', row)
    } a.grid-delete-row-link`;
    this.deleteModeCategoryModal = '#empty_category_grid_delete_categories_modal';
    this.deleteModeInput = position => `#delete_categories_delete_mode_${position} + i`;
    this.deleteModeCategoryModalDiv = '#delete_categories_delete_mode';
    this.submitDeleteCategoryButton = `${this.deleteModeCategoryModal} button.js-submit-delete-categories`;

    // Sort Selectors
    this.tableHead = table => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table, column) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table, column) => `${this.sortColumnDiv(table, column)} span.ps-sort`;

    // Modal products list
    this.deleteProductModal = table => `#${table}-grid-confirm-modal`;
    this.submitDeleteProductButton = table => `${this.deleteProductModal(table)} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = table => `${this.gridPanel(table)} #paginator_select_page_limit`;
    this.paginationLabel = table => `${this.gridPanel(table)} .col-form-label`;
    this.paginationNextLink = table => `${this.gridPanel(table)} #pagination_next_url`;
    this.paginationPreviousLink = table => `${this.gridPanel(table)} [aria-label='Previous']`;
  }

  /* Reset Methods */
  /**
   * Get number of element in table grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of element from
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page, tableName) {
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset filter
   * @return {Promise<void>}
   */
  async resetFilter(page, tableName) {
    if (!(await this.elementNotVisible(page, this.filterResetButton(tableName), 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(tableName));
    }
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of elements
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, tableName) {
    await this.resetFilter(page, tableName);
    return this.getNumberOfElementInGrid(page, tableName);
  }

  /* Filter Methods */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param tableName {string} table name to filter
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page, tableName, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter column not found : ${filterBy}`);
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton(tableName));
  }

  /* table methods */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text from
   * @param row {number} Row on table
   * @param column {string} Column name to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page, tableName, row, column) {
    return this.getTextContent(page, this.tableColumn(tableName, row, column));
  }

  /**
   * Open dropdown menu in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to open dropdown menu
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async openDropdownMenu(page, tableName, row) {
    await Promise.all([
      page.click(this.dropdownToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(tableName, row)}[aria-expanded='true']`),
    ]);
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete product from it
   * @param row {number} Row on table to delete
   * @return {Promise<string>}
   */
  async deleteProductInGrid(page, tableName, row) {
    await this.openDropdownMenu(page, tableName, row);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(tableName, row)),
      this.waitForVisibleSelector(page, `${this.deleteProductModal(tableName)}.show`),
    ]);

    await this.clickAndWaitForNavigation(page, this.submitDeleteProductButton(tableName));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Categories methods */

  /**
   * Delete Row in table empty categories
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete category
   * @param row {number} Row on table to delete
   * @param deletionModePosition {number} value of mode position to delete
   * @return {Promise<string>}
   */
  async deleteCategoryInGrid(page, tableName, row, deletionModePosition) {
    this.dialogListener(page, true);
    await this.openDropdownMenu(page, tableName, row);
    await Promise.all([
      page.click(this.deleteCategoryRowLink(row)),
      this.waitForVisibleSelector(page, this.deleteModeCategoryModal),
    ]);

    // choose deletion mode
    await page.click(this.deleteModeInput(deletionModePosition));
    await this.clickAndWaitForNavigation(page, this.submitDeleteCategoryButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get status
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get status
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page, tableName, row = 1) {
    return this.elementVisible(page, this.enableColumnValidIcon(tableName, row), 100);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows column content
   * @param column {string} Column name to get text column
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, tableName, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(page, this.tableColumn(tableName, i, column));

      if (column === 'active') {
        rowContent = (await this.getStatus(page, tableName, i)).toString();
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, tableName, sortBy, sortDirection = 'asc') {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Methods for pagination
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page, tableName) {
    return this.getTextContent(page, this.paginationLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, tableName, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect(tableName), number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination next
   * @returns {Promise<string>}
   */
  async paginationNext(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination previous
   * @returns {Promise<string>}
   */
  async paginationPrevious(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink(tableName));
    return this.getPaginationLabel(page, tableName);
  }
}

module.exports = new Monitoring();
