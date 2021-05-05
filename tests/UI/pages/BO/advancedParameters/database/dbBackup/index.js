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
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-method='DELETE']`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #backup_grid_bulk_action_delete_backups`;


    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;
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
      this.waitForVisibleSelector(page, this.tableRow(1)),
      this.waitForVisibleSelector(page, this.downloadBackupButton),
    ]);

    return this.getAlertSuccessBlockParagraphContent(page);
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
    this.dialogListener(page, true);
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForNavigation(page, this.deleteRowLink(row));
    return this.getAlertSuccessBlockParagraphContent(page);
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
    return this.getAlertSuccessBlockParagraphContent(page);
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
}

module.exports = new DbBackup();
