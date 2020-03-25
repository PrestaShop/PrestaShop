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
    this.sqlQueryListTableRow = `${this.sqlQueryListForm} tbody tr:nth-child(%ROW)`;
    this.sqlQueryListTableColumn = `${this.sqlQueryListTableRow} td.column-%COLUMN`;
    this.sqlQueryListTableColumnActions = `${this.sqlQueryListTableRow} td.column-actions`;
    this.sqlQueryListTableToggleDropDown = `${this.sqlQueryListTableColumnActions} a[data-toggle='dropdown']`;
    this.sqlQueryListTableViewLink = `${this.sqlQueryListTableColumnActions} a[href*='/view']`;
    this.sqlQueryListTableEditLink = `${this.sqlQueryListTableColumnActions} a[href*='/edit']`;
    this.sqlQueryListTableDeleteLink = `${this.sqlQueryListTableColumnActions} a[href*='/delete']`;
    // Filters
    this.filterInput = `${this.sqlQueryListForm} #sql_request_%FILTERBY`;
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
    await this.setValue(this.filterInput.replace('%FILTERBY', filterBy), value.toString());
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
    return this.getTextContent(this.sqlQueryListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
  }

  /**
   * Go to view sql query page
   * @param row
   * @returns {Promise<void>}
   */
  async goToViewSQLQueryPage(row = 1) {
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown.replace('%ROW', row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.sqlQueryListTableViewLink.replace('%ROW', row));
  }

  /**
   * Go to edit SQL query page
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditSQLQueryPage(row = 1) {
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown.replace('%ROW', row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown}[aria-expanded='true']`.replace('%ROW', row)),
    ]);
    await this.clickAndWaitForNavigation(this.sqlQueryListTableEditLink.replace('%ROW', row));
  }

  async deleteSQLQuery(row = 1) {
    this.dialogListener();
    // Click on dropDown
    await Promise.all([
      this.page.click(this.sqlQueryListTableToggleDropDown.replace('%ROW', row)),
      this.waitForVisibleSelector(
        `${this.sqlQueryListTableToggleDropDown.replace('%ROW', row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete
    await this.clickAndWaitForNavigation(this.sqlQueryListTableDeleteLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
