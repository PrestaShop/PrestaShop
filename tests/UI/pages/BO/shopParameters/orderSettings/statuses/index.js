require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Statuses page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Statuses extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on statuses page
   */
  constructor() {
    super();

    this.pageTitle = 'Statuses •';
    this.successfulUpdateStatusMessage = 'The status has been updated successfully.';

    // Header selectors
    this.newOrderStatusLink = '#page-header-desc-order_return_state-new_order_state';
    this.newOrderReturnStatusLink = '#page-header-desc-order_return_state-new_order_return_state';

    // Form selectors
    this.gridForm = tableName => `#form-${tableName}_state`;
    this.gridTableHeaderTitle = tableName => `${this.gridForm(tableName)} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = tableName => `${this.gridTableHeaderTitle(tableName)} span.badge`;

    // Table selectors
    this.gridTable = tableName => `#table-${tableName}_state`;

    // Filter selectors
    this.filterRow = tableName => `${this.gridTable(tableName)} tr.filter`;
    this.filterColumn = (tableName, filterBy) => `${this.filterRow(tableName)}
    [name='${tableName}_stateFilter_${filterBy}']`;
    this.filterSearchButton = tableName => `#submitFilterButton${tableName}_state`;
    this.filterResetButton = tableName => `button[name='submitReset${tableName}_state']`;

    // Table body selectors
    this.tableBody = tableName => `${this.gridTable(tableName)} tbody`;
    this.tableBodyRows = tableName => `${this.tableBody(tableName)} tr`;
    this.tableBodyRow = (tableName, row) => `${this.tableBodyRows(tableName)}:nth-child(${row})`;
    this.tableBodyColumns = (tableName, row) => `${this.tableBodyRow(tableName, row)} td`;

    // Columns selectors
    this.tableColumn = (tableName, row, column) => `${this.tableBodyColumns(tableName, row)}.column-${column}`;

    // Row actions selectors
    this.tableColumnActions = (tableName, row) => `${this.tableBodyColumns(tableName, row)}
    .btn-group-action`;
    this.tableColumnActionsEditLink = (tableName, row) => `${this.tableColumnActions(tableName, row)} a.edit`;
    this.tableColumnActionsToggleButton = (tableName, row) => `${this.tableColumnActions(tableName, row)}
    button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (tableName, row) => `${this.tableColumnActions(tableName, row)}
    .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (tableName, row) => `${this.tableColumnActionsDropdownMenu(tableName, row)}
    a.delete`;
    this.tableColumnValidIcon = (row, column) => `${this.tableColumn('order', row, column)
    } a.action-enabled`;
    this.tableColumnNotValidIcon = (row, column) => `${this.tableColumn('order', row, column)
    } a.action-disabled`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Growl message
    this.growlMessageBlock = '.growl-message';

    // Pagination selectors
    this.paginationActiveLabel = tableName => `${this.gridForm(tableName)} ul.pagination.pull-right li.active a`;
    this.paginationDiv = tableName => `${this.gridForm(tableName)} .pagination`;
    this.paginationDropdownButton = tableName => `${this.paginationDiv(tableName)} .dropdown-toggle`;
    this.paginationItems = (tableName, number) => `${this.gridForm(tableName)}
    .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = tableName => `${this.gridForm(tableName)} .icon-angle-left`;
    this.paginationNextLink = tableName => `${this.gridForm(tableName)} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = tableName => `${this.gridTable(tableName)} thead`;
    this.sortColumnDiv = (tableName, column) => `${this.tableHead(tableName)} th:nth-child(${column})`;
    this.sortColumnSpanButton = (tableName, column) => `${this.sortColumnDiv(tableName, column)} span.ps-sort`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = tableName => `#bulk_action_menu_${tableName}_state`;
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /* Header methods */

  /**
   * Go to new orders status page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewOrderStatusPage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderStatusLink);
  }

  /**
   * Go to new orders return status page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewOrderReturnStatusPage(page) {
    await this.clickAndWaitForNavigation(page, this.newOrderReturnStatusLink);
  }

  /* Filter methods */

  /**
   * Get Number of element in grid of statuses/return statuses table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of elements
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page, tableName) {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan(tableName));
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset filter
   * @return {Promise<void>}
   */
  async resetFilter(page, tableName) {
    if (!(await this.elementNotVisible(page, this.filterResetButton(tableName), 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton(tableName));
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton(tableName), 2000);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of lines
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page, tableName) {
    await this.resetFilter(page, tableName);

    return this.getNumberOfElementInGrid(page, tableName);
  }

  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to filter
   * @param filterType {string} Type of filter (input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page, tableName, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value.toString());
        await this.clickAndWaitForNavigation(page, this.filterSearchButton(tableName));
        break;

      case 'select':
        await Promise.all([
          page.waitForNavigation({waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text column
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  getTextColumn(page, tableName, row, columnName) {
    return this.getTextContent(page, this.tableColumn(tableName, row, columnName));
  }

  /**
   * Go to edit page
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to edit
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditPage(page, tableName, row) {
    await this.clickAndWaitForNavigation(page, this.tableColumnActionsEditLink(tableName, row));
  }

  /**
   * Delete order status from row
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteOrderStatus(page, tableName, row) {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(tableName, row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(tableName, row));

    // Confirm delete action
    await this.clickAndWaitForNavigation(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page, tableName) {
    return this.getTextContent(page, this.paginationActiveLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination limit
   * @param number {number} Number of pagination to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, tableName, number) {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton(tableName));
    await this.clickAndWaitForNavigation(page, this.paginationItems(tableName, number));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select next page
   * @returns {Promise<string>}
   */
  async paginationNext(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select previous page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page, tableName) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows column content
   * @param columnName {string} Column name of the value to return
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page, tableName, columnName) {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, tableName, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column name to sort with
   * @param columnID {number} Column id of the table
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page, tableName, sortBy, columnID, sortDirection) {
    const sortColumnButton = `${this.sortColumnDiv(tableName, columnID)} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForNavigation(page, sortColumnButton);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk select rows
   * @return {Promise<void>}
   */
  async bulkSelectRows(page, tableName) {
    await page.click(this.bulkActionMenuButton(tableName));

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Delete order statuses by bulk action
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk delete
   * @returns {Promise<string>}
   */
  async bulkDeleteOrderStatuses(page, tableName) {
    this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page, tableName);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton(tableName));

    // Click on delete
    await this.clickAndWaitForNavigation(page, this.bulkDeleteLink);
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get Value of column Displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get status
   * @returns {Promise<boolean>}
   */
  getStatus(page, row, columnName) {
    return this.elementVisible(page, this.tableColumnValidIcon(row, columnName), 100);
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} column name on table
   * @param valueWanted {boolean} True if we need to enable status
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page, row, columnName, valueWanted = true) {
    const columnSelector = this.tableColumn('order', row, columnName);

    await this.waitForVisibleSelector(page, columnSelector, 2000);

    if (await this.getStatus(page, row, columnName) !== valueWanted) {
      await page.click(columnSelector);
      await this.waitForVisibleSelector(
        page,
        (valueWanted ? this.tableColumnValidIcon(row, columnName) : this.tableColumnNotValidIcon(row, columnName)),
      );

      return true;
    }

    return false;
  }
}

module.exports = new Statuses();
