require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class SqlManager extends BOBasePage {
  constructor() {
    super();
    this.pageTitle = 'SQL Manager •';
    this.successfulDeleteMessage = 'Successful deletion';

    // Header Selectors
    this.addNewSQLQueryButton = '#page-header-desc-configuration-add';
    this.dbBackupSubTabLink = '#subtab-AdminBackup';

    // List of SQL query
    this.sqlQueryGridPanel = '#sql_request_grid_panel';
    this.sqlQueryGridTitle = `${this.sqlQueryGridPanel} h3.card-header-title`;
    this.sqlQueryListForm = '#sql_request_grid';
    this.sqlQueryListTableRow = row => `${this.sqlQueryListForm} tbody tr:nth-child(${row})`;
    this.sqlQueryListTableColumn = (row, column) => `${this.sqlQueryListTableRow(row)} td.column-${column}`;
    this.sqlQueryListTableColumnActions = row => `${this.sqlQueryListTableRow(row)} td.column-actions`;
    this.sqlQueryListTableToggleDropDown = row => `${this.sqlQueryListTableColumnActions(row)
    } a[data-toggle='dropdown']`;
    this.sqlQueryListTableViewLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/view']`;
    this.sqlQueryListTableEditLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/edit']`;
    this.sqlQueryListTableDeleteLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/delete']`;
    this.sqlQueryListTableExportLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/export']`;

    // Filters
    this.filterInput = filterBy => `${this.sqlQueryListForm} #sql_request_${filterBy}`;
    this.filterSearchButton = `${this.sqlQueryListForm} button[name='sql_request[actions][search]']`;
    this.filterResetButton = `${this.sqlQueryListForm} button[name='sql_request[actions][reset]']`;

    // Sort Selectors
    this.tableHead = `${this.sqlQueryListForm} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.sqlQueryGridPanel} .col-form-label`;
    this.paginationNextLink = `${this.sqlQueryGridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.sqlQueryGridPanel} [aria-label='Previous']`;

    // Bulk Actions
    this.selectAllRowsDiv = `${this.sqlQueryListForm} #sql_request_grid_bulk_action_select_all + i`;
    this.bulkActionsToggleButton = `${this.sqlQueryListForm} button.dropdown-toggle`;
    this.bulkActionsDeleteButton = `${this.sqlQueryListForm} #sql_request_grid_bulk_action_delete_all`;
  }

  /* Header Methods */
  /**
   * Go to db Backup page
   * @param page
   * @return {Promise<void>}
   */
  async goToDbBackupPage(page) {
    await this.clickAndWaitForNavigation(page, this.dbBackupSubTabLink);
  }

  /**
   * Go to new SQL query page
   * @param page
   * @returns {Promise<void>}
   */
  async goToNewSQLQueryPage(page) {
    await this.clickAndWaitForNavigation(page, this.addNewSQLQueryButton);
  }

  /**
   * Reset filter
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.sqlQueryGridTitle);
  }

  /**
   * Reset input filters
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter SQL manager table
   * @param page
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterSQLQuery(page, filterBy, value = '') {
    await this.setValue(page, this.filterInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * Get text column from table
   * @param page
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  getTextColumnFromTable(page, row, column) {
    return this.getTextContent(page, this.sqlQueryListTableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page
   * @param column
   * @return {Promise<[]>}
   */
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to view sql query page
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToViewSQLQueryPage(page, row = 1) {
    await Promise.all([
      page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForNavigation(page, this.sqlQueryListTableViewLink(row));
  }

  /**
   * Go to edit SQL query page
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditSQLQueryPage(page, row = 1) {
    await Promise.all([
      page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.sqlQueryListTableEditLink(row));
  }

  /**
   * Delete SQL query
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteSQLQuery(page, row = 1) {
    this.dialogListener(page);
    // Click on dropDown
    await Promise.all([
      page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        page,
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(page, this.sqlQueryListTableDeleteLink(row));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Export sql result to csv
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async exportSqlResultDataToCsv(page, row = 1) {
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      await page.click(this.sqlQueryListTableExportLink(row)),
    ]);
    return download.path();
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page
   * @param sortBy, column to sort with
   * @param sortDirection, asc or desc
   * @return {Promise<void>}
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

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page
   * @param number
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
   * @param page
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Delete all sql queries with Bulk Actions
   * @param page
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page) {
    this.dialogListener(page, true);
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsDiv, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new SqlManager();
