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

  private readonly gridPanel: (tableName: string) => string;

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

  private readonly tableColumnActionsStatus: (tableName: string, row: number, column: string) => string;

  private readonly tableColumnActionsStatusToggleInput: (tableName: string, row: number, column: string) => string;

  private readonly confirmDeleteModal: (tableName: string) => string;

  private readonly confirmDeleteButton: (tableName: string) => string;

  private readonly paginationLimitSelect: (tableName: string) => string;

  private readonly paginationLabel: (tableName: string) => string;

  private readonly paginationNextLink: (tableName: string) => string;

  private readonly paginationPreviousLink: (tableName: string) => string;

  private readonly tableHead: (tableName: string) => string;

  private readonly sortColumnDiv: (tableName: string, column: number) => string;

  private readonly sortColumnSpanButton: (tableName: string, column: number) => string;

  private readonly deleteSelectionButton: (tableName: string) => string;

  private readonly selectAllLabel: (tableName: string) => string;

  private readonly bulkActionsToggleButton: (tableName: string) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on statuses page
   */
  constructor() {
    super();

    this.pageTitle = 'Statuses •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header selectors
    this.newOrderStatusLink = '#page-header-desc-configuration-add[title=\'Add new order status\']';
    this.newOrderReturnStatusLink = '#page-header-desc-configuration-add_return_state[title=\'Add new return status\']';

    // Form selectors
    this.gridPanel = (tableName: string) => `#${tableName}_states_grid_panel`;
    this.gridForm = (tableName: string) => `#${tableName}_states_filter_form`;
    this.gridTableHeaderTitle = (tableName: string) => `${this.gridForm(tableName)} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = (tableName: string) => `${this.gridPanel(tableName)} h3.card-header-title`;

    // Table selectors
    this.gridTable = (tableName: string) => `#${tableName}_states_grid_table`;

    // Filter selectors
    this.filterRow = (tableName: string) => `${this.gridTable(tableName)} tr.column-filters`;
    this.filterColumn = (tableName: string, filterBy: string) => `${this.filterRow(tableName)} `
      + `#${tableName}_states_${filterBy}`;
    this.filterSearchButton = (tableName: string) => `${this.gridTable(tableName)} .grid-search-button`;
    this.filterResetButton = (tableName: string) => `${this.gridTable(tableName)} .grid-reset-button`;

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
      + ' a.grid-edit-row-link';
    this.tableColumnActionsToggleButton = (tableName: string, row: number) => `${this.tableColumnActions(tableName, row)}`
      + ' button.dropdown-toggle';
    this.tableColumnActionsDropdownMenu = (tableName: string, row: number) => `${this.tableColumnActions(tableName, row)}`
      + ' a[data-toggle=\'dropdown\']';
    this.tableColumnActionsDeleteLink = (tableName: string, row: number) => `${this.tableColumnActions(
      tableName, row)} a.grid-delete-row-link`;
    this.tableColumnActionsStatus = (tableName: string, row: number, column: string) => `${
      this.tableColumn(tableName, row, column)} .ps-switch`;
    this.tableColumnActionsStatusToggleInput = (tableName: string, row: number, column: string) => `${
      this.tableColumnActionsStatus(tableName, row, column)} input`;

    // Confirmation modal
    this.confirmDeleteModal = (tableName: string) => `#${tableName}_states-grid-confirm-modal`;
    this.confirmDeleteButton = (tableName: string) => `${this.confirmDeleteModal(tableName)} button.btn-confirm-submit`;

    // Growl message
    this.growlMessageBlock = '.growl-message';

    // Pagination selectors
    this.paginationLimitSelect = (tableName: string) => `${this.gridPanel(tableName)} #paginator_select_page_limit`;
    this.paginationLabel = (tableName: string) => `${this.gridPanel(tableName)} .col-form-label`;
    this.paginationNextLink = (tableName: string) => `${this.gridPanel(tableName)} [data-role=next-page-link]`;
    this.paginationPreviousLink = (tableName: string) => `${this.gridPanel(tableName)} [data-role='previous-page-link']`;

    // Sort Selectors
    this.tableHead = (tableName: string) => `${this.gridTable(tableName)} thead`;
    this.sortColumnDiv = (tableName: string, column: number) => `${this.tableHead(tableName)} th:nth-child(${column})`;
    this.sortColumnSpanButton = (tableName: string, column: number) => `${this.sortColumnDiv(tableName, column)} span.ps-sort`;

    // Bulk actions selectors
    this.deleteSelectionButton = (tableName: string) => `${this.gridPanel(tableName)} #${
      tableName}_states_grid_bulk_action_delete_selection`;
    this.selectAllLabel = (tableName: string) => `${this.gridPanel(tableName)} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = (tableName: string) => `${this.gridPanel(tableName)} button.js-bulk-actions-btn`;
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
   * @param tableName {string} Table name
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page, tableName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan(tableName));
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of lines
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
    // Add listener to dialog to accept deletion
    await this.dialogListener(page, true);
    // Click on dropDown
    await Promise.all([
      page.click(this.tableColumnActionsDropdownMenu(tableName, row)),
      this.waitForVisibleSelector(page, `${this.tableColumnActionsDropdownMenu(tableName, row)}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await page.click(this.tableColumnActionsDeleteLink(tableName, row));
    await this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`);
    // Confirm delete action
    await this.clickAndWaitForURL(page, this.confirmDeleteButton(tableName));

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page, tableName: string): Promise<string> {
    return this.getTextContent(page, this.paginationLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination limit
   * @param number {number} Number of pagination to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, tableName: string, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect(tableName), number.toString());

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
    const sortColumnDiv = `${this.sortColumnDiv(tableName, columnID)} [data-sort-direction='${sortDirection}']`;

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForLoadState(page, this.sortColumnSpanButton(tableName, columnID));
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Bulk actions methods */
  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk select rows
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page, tableName: string): Promise<void> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllLabel(tableName), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}:not([disabled])`),
    ]);
  }

  /**
   * Delete order statuses by bulk action
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to bulk delete
   * @returns {Promise<string>}
   */
  async bulkDeleteOrderStatuses(page: Page, tableName: string): Promise<string> {
    // Select all rows
    await this.bulkSelectRows(page, tableName);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton(tableName)),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton(tableName)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteSelectionButton(tableName)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal(tableName)}.show`),
    ]);
    await this.clickAndWaitForURL(page, this.confirmDeleteButton(tableName));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get Value of column Displayed
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param row {number} Row on table
   * @param columnName {string} Column name to get status
   * @returns {Promise<boolean>}
   */
  async getStatus(page: Page, tableName: string, row: number, columnName: string): Promise<boolean> {
    const inputValue = await this.getAttributeContent(
      page,
      `${this.tableColumnActionsStatusToggleInput(tableName, row, columnName)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Quick edit toggle column value
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param row {number} Row on table
   * @param columnName {string} column name on table
   * @param valueWanted {boolean} True if we need to enable status
   * @returns {Promise<boolean>} return true if action is done, false otherwise
   */
  async setStatus(page: Page, tableName: string, row: number, columnName: string, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, tableName, row, columnName) !== valueWanted) {
      await this.clickAndWaitForLoadState(page, this.tableColumnActionsStatus(tableName, row, columnName));
      return true;
    }

    return false;
  }
}

export default new Statuses();
