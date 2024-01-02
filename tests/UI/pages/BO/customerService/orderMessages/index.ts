import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Order messages listing page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class OrderMessages extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newOrderMessageLink: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  /**
   * @constructs
   * Setting up titles and selectors to use on order messages listing page
   */
  constructor() {
    super();

    this.pageTitle = `Order messages • ${global.INSTALL.SHOP_NAME}`;

    // Selectors header
    this.newOrderMessageLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#order_message_grid_panel';
    this.gridTable = '#order_message_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #order_message_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-edit-row-link`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#order_message_grid_bulk_action_delete_selection';
    // Delete modal
    this.confirmDeleteModal = '#order_message-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;
    // Sort Selectors
    this.tableHead = `${this.gridPanel} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header Methods */
  /**
   * Go to new order message page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewOrderMessagePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newOrderMessageLink);
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string|number} Value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterBy: string, value: string): Promise<void> {
    await this.setValue(page, this.filterColumn(filterBy), value);
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /* Column Methods */
  /**
   * Edit order message
   * @param page {Page} Browser tab
   * @param row {number} Row to click on
   * @return {Promise<void>}
   */
  async gotoEditOrderMessage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row to delete
   * @returns {Promise<string>}
   */
  async deleteOrderMessage(page: Page, row: number = 1): Promise<string> {
    await Promise.all([
      page.locator(this.dropdownToggleButton(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteRowLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    // Confirm delete in modal
    await this.confirmDeleteOrderMessages(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row to delete
   * @param column {string} which column to get text from
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /* Bulk Actions Methods */
  /**
   * Delete with bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    // Click on delete and wait for modal
    await this.confirmDeleteOrderMessages(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteOrderMessages(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination value to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);
    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);
    return this.getPaginationLabel(page);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text from
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextContent(page, this.tableColumn(i, column));
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction "asc"/"desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }
}

export default new OrderMessages();
