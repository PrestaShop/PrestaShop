import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Sql manager page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class SqlManager extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewSQLQueryButton: string;

  private readonly dbBackupSubTabLink: string;

  private readonly sqlQueryGridPanel: string;

  private readonly sqlQueryGridTitle: string;

  private readonly sqlQueryListForm: string;

  private readonly sqlQueryListTableRow: (row: number) => string;

  private readonly sqlQueryListTableColumn: (row: number, column: string) => string;

  private readonly sqlQueryListTableColumnActions: (row: number) => string;

  private readonly sqlQueryListTableToggleDropDown: (row: number) => string;

  private readonly sqlQueryListTableViewLink: (row: number) => string;

  private readonly sqlQueryListTableEditLink: (row: number) => string;

  private readonly sqlQueryListTableDeleteLink: (row: number) => string;

  private readonly sqlQueryListTableExportLink: (row: number) => string;

  private readonly filterInput: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly selectAllRowsDiv: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly deleteModal: string;

  private readonly modalDeleteButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on sql manager page
   */
  constructor() {
    super();

    this.pageTitle = `SQL manager • ${global.INSTALL.SHOP_NAME}`;
    this.successfulDeleteMessage = 'Successful deletion';

    // Header Selectors
    this.addNewSQLQueryButton = '#page-header-desc-configuration-add';
    this.dbBackupSubTabLink = '#subtab-AdminBackup';

    // List of SQL query
    this.sqlQueryGridPanel = '#sql_request_grid_panel';
    this.sqlQueryGridTitle = `${this.sqlQueryGridPanel} h3.card-header-title`;
    this.sqlQueryListForm = '#sql_request_grid';
    this.sqlQueryListTableRow = (row: number) => `${this.sqlQueryListForm} tbody tr:nth-child(${row})`;
    this.sqlQueryListTableColumn = (row: number, column: string) => `${this.sqlQueryListTableRow(row)} td.column-${column}`;
    this.sqlQueryListTableColumnActions = (row: number) => `${this.sqlQueryListTableRow(row)} td.column-actions`;
    this.sqlQueryListTableToggleDropDown = (row: number) => `${this.sqlQueryListTableColumnActions(row)
    } a[data-toggle='dropdown']`;
    this.sqlQueryListTableViewLink = (row: number) => `${this.sqlQueryListTableColumnActions(row)} a.grid-view-row-link`;
    this.sqlQueryListTableEditLink = (row: number) => `${this.sqlQueryListTableColumnActions(row)} a.grid-edit-row-link`;
    this.sqlQueryListTableDeleteLink = (row: number) => `${this.sqlQueryListTableColumnActions(row)} a.grid-delete-row-link`;
    this.sqlQueryListTableExportLink = (row: number) => `${this.sqlQueryListTableColumnActions(row)} a.grid-export-row-link`;

    // Filters
    this.filterInput = (filterBy: string) => `${this.sqlQueryListForm} #sql_request_${filterBy}`;
    this.filterSearchButton = `${this.sqlQueryListForm} .grid-search-button`;
    this.filterResetButton = `${this.sqlQueryListForm} .grid-reset-button`;

    // Delete modal
    this.confirmDeleteModal = '#sql_request-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Sort Selectors
    this.tableHead = `${this.sqlQueryListForm} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.sqlQueryGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.sqlQueryGridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.sqlQueryGridPanel} [data-role=previous-page-link]`;

    // Bulk Actions
    this.selectAllRowsDiv = `${this.sqlQueryListForm} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.sqlQueryListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.sqlQueryListForm} #sql_request_grid_bulk_action_delete_selection`;

    // Modal Dialog
    this.deleteModal = '#sql_request-grid-confirm-modal.show';
    this.modalDeleteButton = `${this.deleteModal} button.btn-confirm-submit`;
  }

  /* Header Methods */
  /**
   * Go to db Backup page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToDbBackupPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.dbBackupSubTabLink);
  }

  /**
   * Go to new SQL query page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToNewSQLQueryPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewSQLQueryButton);
  }

  /**
   * Reset filter
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.sqlQueryGridTitle);
  }

  /**
   * Reset input filters
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter SQL manager table
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @returns {Promise<void>}
   */
  async filterSQLQuery(page: Page, filterBy: string, value: string = ''): Promise<void> {
    await this.setValue(page, this.filterInput(filterBy), value);
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Get text column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.sqlQueryListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get text value
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to view sql query page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToViewSQLQueryPage(page: Page, row: number = 1): Promise<void> {
    await Promise.all([
      page.locator(this.sqlQueryListTableToggleDropDown(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForURL(page, this.sqlQueryListTableViewLink(row));
  }

  /**
   * Go to edit SQL query page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditSQLQueryPage(page: Page, row: number = 1): Promise<void> {
    await Promise.all([
      page.locator(this.sqlQueryListTableToggleDropDown(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForURL(page, this.sqlQueryListTableEditLink(row));
  }

  /**
   * Delete SQL query
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteSQLQuery(page: Page, row: number = 1): Promise<string> {
    // Click on dropDown
    await Promise.all([
      page.locator(this.sqlQueryListTableToggleDropDown(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.sqlQueryListTableDeleteLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);

    await this.confirmDeleteSQLQuery(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Export sql result to csv
   * @param page {Page} Browser tab
   * @param row {number} Row of the sql result on table
   * @returns {Promise<string|null>}
   */
  exportSqlResultDataToCsv(page: Page, row: number = 1): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.sqlQueryListTableExportLink(row));
  }

  /**
   * Confirm delete with modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteSQLQuery(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

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

  /**
   * Delete all sql queries with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsDiv).evaluate((el: HTMLElement) => el.click()),
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
      this.waitForVisibleSelector(page, this.deleteModal),
    ]);
    await this.clickAndWaitForURL(page, this.modalDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new SqlManager();
