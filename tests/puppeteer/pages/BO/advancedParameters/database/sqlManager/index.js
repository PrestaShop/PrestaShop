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
    this.sqlQueryListTableViewLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/view']`;
    this.sqlQueryListTableEditLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/edit']`;
    this.sqlQueryListTableDeleteLink = row => `${this.sqlQueryListTableColumnActions(row)} a[href*='/delete']`;
    // Filters
    this.filterInput = filterBy => `${this.sqlQueryListForm} #sql_request_${filterBy}`;
    this.filterSearchButton = `${this.sqlQueryListForm} button[name='sql_request[actions][search]']`;
    this.filterResetButton = `${this.sqlQueryListForm} button[name='sql_request[actions][reset]']`;
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
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.sqlQueryGridTitle);
  }

  /**
   * Reset input filters
   * @return {Promise<integer>}
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
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown(row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.sqlQueryListTableDeleteLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
