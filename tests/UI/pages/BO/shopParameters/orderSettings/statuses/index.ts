import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Statuses page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Statuses extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly newOrderStatusLink: string;

  private readonly newOrderReturnStatusLink: string;

  private readonly gridForm: (tableName: string) => string;

  private readonly gridTableHeaderTitle: (tableName: string) => string;

  private readonly gridTableNumberOfTitlesSpan: (tableName: string) => string;

  private readonly gridTable: (tableName: string) => string;

  private readonly filterRow: (tableName: string) => string;

  private readonly filterColumn: (tableName: string, filterBy: string) => string;

  private readonly filterSearchButton: (tableName: string) => string;

  private readonly filterResetButton: (tableName: string) => string;

  private readonly tableBody: (tableName: string) => string;

  private readonly tableBodyRows: (tableName: string) => string;

  private readonly tableBodyRow: (tableName: string, row: number) => string;

  private readonly tableBodyColumns: (tableName: string, row: number) => string;

  private readonly tableColumn: (tableName: string, row: number, column: string) => string;

  private readonly tableColumnActions: (tableName: string, row: number) => string;

  private readonly tableColumnActionsEditLink: (tableName: string, row: number) => string;

  private readonly tableColumnActionsToggleButton: (tableName: string, row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (tableName: string, row: number) => string;

  private readonly tableColumnActionsDeleteLink: (tableName: string, row: number) => string;

  private readonly tableColumnValidIcon: (row: number, column: string) => string;

  private readonly tableColumnNotValidIcon: (row: number, column: string) => string;

  private readonly deleteModalButtonYes: string;

  private readonly paginationActiveLabel: (tableName: string) => string;

  private readonly paginationDiv: (tableName: string) => string;

  private readonly paginationDropdownButton: (tableName: string) => string;

  private readonly paginationItems: (tableName: string, number: number) => string;

  private readonly paginationPreviousLink: (tableName: string) => string;

  private readonly paginationNextLink: (tableName: string) => string;

  private readonly tableHead: (tableName: string) => string;

  private readonly sortColumnDiv: (tableName: string, column: number) => string;

  private readonly sortColumnSpanButton: (tableName: string, column: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: (tableName: string) => string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

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
    this.gridForm = (tableName: string) => `#form-${tableName}_state`;
    this.gridTableHeaderTitle = (tableName: string) => `${this.gridForm(tableName)} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = (tableName: string) => `${this.gridTableHeaderTitle(tableName)} span.badge`;

    // Table selectors
    this.gridTable = (tableName: string) => `#table-${tableName}_state`;

    // Filter selectors
    this.filterRow = (tableName: string) => `${this.gridTable(tableName)} tr.filter`;
    this.filterColumn = (tableName: string, filterBy: string) => `${this.filterRow(tableName)}
    [name='${tableName}_stateFilter_${filterBy}']`;
    this.filterSearchButton = (tableName: string) => `#submitFilterButton${tableName}_state`;
    this.filterResetButton = (tableName: string) => `button[name='submitReset${tableName}_state']`;

    // Table body selectors
    this.tableBody = (tableName: string) => `${this.gridTable(tableName)} tbody`;
    this.tableBodyRows = (tableName: string) => `${this.tableBody(tableName)} tr`;
    this.tableBodyRow = (tableName: string, row: number) => `${this.tableBodyRows(tableName)}:nth-child(${row})`;
    this.tableBodyColumns = (tableName: string, row: number) => `${this.tableBodyRow(tableName, row)} td`;

    // Columns selectors
    this.tableColumn = (tableName: string, row: number, column: string) => `${this.tableBodyColumns(tableName, row)}`
      + `.column-${column}`;

    // Row actions selectors
    this.tableColumnActions = (tableName: string, row: number) => `${this.tableBodyColumns(tableName, row)}`
      + ' .btn-group-action';
    this.tableColumnActionsEditLink = (tableName: string, row: number) => `${this.tableColumnActions(tableName, row)}`
      + ' a.edit';
    this.tableColumnActionsToggleButton = (tableName: string, row: number) => `${this.tableColumnActions(tableName, row)}`
      + ' button.dropdown-toggle';
    this.tableColumnActionsDropdownMenu = (tableName: string, row: number) => `${this.tableColumnActions(tableName, row)}`
      + ' .dropdown-menu';
    this.tableColumnActionsDeleteLink = (tableName: string, row: number) => `${this.tableColumnActionsDropdownMenu(
      tableName, row)} a.delete`;
    this.tableColumnValidIcon = (row: number, column: string) => `${this.tableColumn('order', row, column)
    } a.action-enabled`;
    this.tableColumnNotValidIcon = (row: number, column: string) => `${this.tableColumn('order', row, column)
    } a.action-disabled`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Growl message
    this.growlMessageBlock = '.growl-message';

    // Pagination selectors
    this.paginationActiveLabel = (tableName: string) => `${this.gridForm(tableName)} ul.pagination.pull-right li.active a`;
    this.paginationDiv = (tableName: string) => `${this.gridForm(tableName)} .pagination`;
    this.paginationDropdownButton = (tableName: string) => `${this.paginationDiv(tableName)} .dropdown-toggle`;
    this.paginationItems = (tableName: string, number: number) => `${this.gridForm(tableName)}
    .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = (tableName: string) => `${this.gridForm(tableName)} .icon-angle-left`;
    this.paginationNextLink = (tableName: string) => `${this.gridForm(tableName)} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = (tableName: string) => `${this.gridTable(tableName)} thead`;
    this.sortColumnDiv = (tableName: string, column: number) => `${this.tableHead(tableName)} th:nth-child(${column})`;
    this.sortColumnSpanButton = (tableName: string, column: number) => `${this.sortColumnDiv(tableName, column)} span.ps-sort`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = (tableName: string) => `#bulk_action_menu_${tableName}_state`;
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
  async goToNewOrderStatusPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newOrderStatusLink);
  }

  /**
   * Go to new orders return status page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewOrderReturnStatusPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newOrderReturnStatusLink);
  }

  /* Filter methods */

  /**
   * Get Number of element in grid of statuses/return statuses table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of elements
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page, tableName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan(tableName));
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset filter
   * @return {Promise<void>}
   */
  async resetFilter(page: Page, tableName: string): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton(tableName), 2000)) {
      await page.click(this.filterResetButton(tableName));
      await this.elementNotVisible(page, this.filterResetButton(tableName), 2000);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton(tableName), 2000);
  }

  /**
   * Reset and get number of lines
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of lines
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page, tableName: string): Promise<number> {
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
  async filterTable(page: Page, tableName: string, filterType: string, filterBy: string, value: string): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value.toString());
        await page.click(this.filterSearchButton(tableName));
        break;

      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value === '1' ? 'Yes' : 'No');
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }

    await this.elementVisible(page, this.filterResetButton(tableName), 2000);
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
  getTextColumn(page: Page, tableName: string, row: number, columnName: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(tableName, row, columnName));
  }

  /**
   * Go to edit page
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to edit
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditPage(page: Page, tableName: string, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(tableName, row));
  }

  /**
   * Delete order status from row
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteOrderStatus(page: Page, tableName: string, row: number): Promise<string> {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(tableName, row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(tableName, row));

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

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
  getPaginationLabel(page: Page, tableName: string): Promise<string> {
    return this.getTextContent(page, this.paginationActiveLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination limit
   * @param number {number} Number of pagination to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, tableName: string, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton(tableName));
    await page.click(this.paginationItems(tableName, number));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select next page
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page, tableName: string): Promise<string> {
    await page.click(this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select previous page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page, tableName: string): Promise<string> {
    await page.click(this.paginationPreviousLink(tableName));

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
  async getAllRowsColumnContent(page: Page, tableName: string, columnName: string): Promise<string[]> {
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
  async sortTable(page: Page, tableName: string, sortBy: string, columnID: number, sortDirection: string): Promise<void> {
    const sortColumnButton: string = `${this.sortColumnDiv(tableName, columnID)} i.icon-caret-${sortDirection}`;

    await this.clickAndWaitForURL(page, sortColumnButton);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk select rows
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page, tableName: string): Promise<void> {
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
  async bulkDeleteOrderStatuses(page: Page, tableName: string): Promise<string> {
    await this.dialogListener(page, true);
    // Select all rows
    await this.bulkSelectRows(page, tableName);

    // Click on Button Bulk actions
    await page.click(this.bulkActionMenuButton(tableName));

    // Click on delete
    await this.clickAndWaitForURL(page, this.bulkDeleteLink);
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get Value of column Displayed
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get status
   * @returns {Promise<boolean>}
   */
  getStatus(page: Page, row: number, columnName: string): Promise<boolean> {
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
  async setStatus(page: Page, row: number, columnName: string, valueWanted: boolean = true): Promise<boolean> {
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

export default new Statuses();
