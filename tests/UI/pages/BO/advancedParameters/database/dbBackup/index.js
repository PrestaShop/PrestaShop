require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class DbBackup extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'DB Backup •';
    this.successfulBackupCreationMessage = 'It appears the backup was successful, however you must download '
      + 'and carefully verify the backup file before proceeding.';

    // Header selectors
    this.sqlManagerSubTabLink = '#subtab-AdminRequestSql';
    // New Backup for selectors
    this.newBackupForm = 'form[action*=\'backups/new\']';
    this.newBackupButton = `${this.newBackupForm} button`;
    // Download backup selectors
    this.downloadBackupButton = 'a[href*=\'backups/download\']';
    // DB backup grid selectors
    this.gridPanel = '#backup_grid_panel';
    this.gridTable = '#backup_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} div.card-header h3`;
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // Actions buttons in Row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-method='DELETE']`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #backup_grid_bulk_action_delete_backups`;
  }

  /* Header methods */
  /**
   * Go to db Backup page
   * @return {Promise<void>}
   */
  async goToSqlManagerPage() {
    await this.clickAndWaitForNavigation(this.sqlManagerSubTabLink);
  }

  /* Form and grid methods */
  /**
   * Get number of backups
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Create new db backup
   * @return {Promise<textContent>}
   */
  async createDbDbBackup() {
    await Promise.all([
      this.page.click(this.newBackupButton),
      this.page.waitForSelector(this.tableRow(1), {state: 'visible'}),
      this.page.waitForSelector(this.downloadBackupButton, {state: 'visible'}),
    ]);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Download backup
   * @return {Promise<void>}
   */
  async downloadDbBackup() {
    const [download] = await Promise.all([
      this.page.waitForEvent('download'),
      await this.page.click(this.downloadBackupButton),
    ]);
    return download.path();
  }

  /**
   * Delete backup
   * @param row
   * @return {Promise<textContent>}
   */
  async deleteBackup(row) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.page.waitForSelector(`${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Delete with bulk actions
   * @return {Promise<textContent>}
   */
  async deleteWithBulkActions() {
    this.dialogListener(true);
    // Click on Select All
    await Promise.all([
      this.page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      this.page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(`${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(this.bulkActionsDeleteButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
