require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class SqlManager extends BOBasePage {
  constructor(page) {
    super(page);

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
    this.sqlQueryListTableViewLink = row => `${this.sqlQueryListTableColumnActions(row)} a.grid-view-row-link`;
    this.sqlQueryListTableEditLink = row => `${this.sqlQueryListTableColumnActions(row)} a.grid-edit-row-link`;
    this.sqlQueryListTableDeleteLink = row => `${this.sqlQueryListTableColumnActions(row)} a.grid-delete-row-link`;
    this.sqlQueryListTableExportLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/export']`;
    // Filters
    this.filterInput = filterBy => `${this.sqlQueryListForm} #sql_request_${filterBy}`;
    this.filterSearchButton = `${this.sqlQueryListForm} .grid-search-button`;
    this.filterResetButton = `${this.sqlQueryListForm} .grid-reset-button`;

    // Delete modal
    this.confirmDeleteModal = '#sql_request-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /* Header Methods */
  /**
   * Go to db Backup page
   * @return {Promise<void>}
   */
  async goToDbBackupPage() {
    await this.clickAndWaitForNavigation(this.dbBackupSubTabLink);
  }

  /**
   * Go to new SQL query page
   * @returns {Promise<void>}
   */
  async goToNewSQLQueryPage() {
    await this.clickAndWaitForNavigation(this.addNewSQLQueryButton);
  }

  /**
   * Reset filter
   * @returns {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.sqlQueryGridTitle);
  }

  /**
   * Reset input filters
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter SQL manager table
   * @param filterBy
   * @param value
   * @returns {Promise<void>}
   */
  async filterSQLQuery(filterBy, value = '') {
    await this.setValue(this.filterInput(filterBy), value.toString());
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Get text column from table
   * @param row
   * @param column
   * @returns {Promise<string>}
   */
  getTextColumnFromTable(row, column) {
    return this.getTextContent(this.sqlQueryListTableColumn(row, column));
  }

  /**
   * Go to view sql query page
   * @param row
   * @returns {Promise<void>}
   */
  async goToViewSQLQueryPage(row = 1) {
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForNavigation(this.sqlQueryListTableViewLink(row));
  }

  /**
   * Go to edit SQL query page
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditSQLQueryPage(row = 1) {
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.sqlQueryListTableEditLink(row));
  }

  /**
   * Delete SQL query
   * @param row
   * @returns {Promise<string>}
   */
  async deleteSQLQuery(row = 1) {
    // Click on dropDown
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.sqlQueryListTableDeleteLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSQLQuery();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Export sql result to csv
   * @param row
   * @returns {Promise<void>}
   */
  async exportSqlResultDataToCsv(row = 1) {
    const [download] = await Promise.all([
      this.page.waitForEvent('download'),
      await this.page.click(this.sqlQueryListTableExportLink(row)),
    ]);
    return download.path();
  }

  /**
   * Confirm delete with modal
   * @return {Promise<void>}
   */
  async confirmDeleteSQLQuery() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
  }
};
