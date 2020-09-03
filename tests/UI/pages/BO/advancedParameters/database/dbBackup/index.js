require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class DbBackup extends BOBasePage {
  constructor() {
    super();

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
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #backup_grid_bulk_action_delete_backups`;
    this.confirmDeleteModal = '#backup-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /* Header methods */
  /**
   * Go to db Backup page
   * @param page
   * @returns {Promise<void>}
   */
  async goToSqlManagerPage(page) {
    await this.clickAndWaitForNavigation(page, this.sqlManagerSubTabLink);
  }

  /* Form and grid methods */
  /**
   * Get number of backups
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Create new db backup
   * @param page
   * @returns {Promise<string>}
   */
  async createDbDbBackup(page) {
    await Promise.all([
      page.click(this.newBackupButton),
      page.waitForSelector(this.tableRow(1), {state: 'visible'}),
      page.waitForSelector(this.downloadBackupButton, {state: 'visible'}),
    ]);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Download backup
   * @param page
   * @return {Promise<void>}
   */
  async downloadDbBackup(page) {
    const [download] = await Promise.all([
      page.waitForEvent('download'),
      await page.click(this.downloadBackupButton),
    ]);
    return download.path();
  }

  /**
   * Delete backup
   * @param page
   * @param row
   * @returns {Promise<string>}
   */
  async deleteBackup(page, row) {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      page.waitForSelector(`${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteDbBackups(page);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete with in modal
   * @param page
   * @return {Promise<void>}
   */
  async confirmDeleteDbBackups(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Delete with bulk actions
   * @param page
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page) {
    this.dialogListener(page, true);
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, el => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await this.clickAndWaitForNavigation(page, this.bulkActionsDeleteButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new DbBackup();
