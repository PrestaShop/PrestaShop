import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * DB Backup page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class DbBackup extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulBackupCreationMessage: string;

  private readonly sqlManagerSubTabLink: string;

  private readonly newBackupButton: string;

  private readonly downloadBackupButton: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly actionsColumn: (row: number) => string;

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

  /**
   * @constructs
   * Setting up texts and selectors to use on db backup page
   */
  constructor() {
    super();

    this.pageTitle = 'DB Backup •';
    this.successfulBackupCreationMessage = 'It appears the backup was successful, however you must download '
      + 'and carefully verify the backup file before proceeding.';

    // Header selectors
    this.sqlManagerSubTabLink = '#subtab-AdminRequestSql';

    // New Backup for selectors
    this.newBackupButton = 'button[data-role=create-backup-btn]';

    // Download backup selectors
    this.downloadBackupButton = 'a.download-file-link';

    // DB backup grid selectors
    this.gridPanel = '#backup_grid_panel';
    this.gridTable = '#backup_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} div.card-header h3`;
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;

    // Actions buttons in Row
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #backup_grid_bulk_action_delete_selection`;
    this.confirmDeleteModal = '#backup-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role=previous-page-link]`;
  }

  /* Header methods */
  /**
   * Go to db Backup page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToSqlManagerPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.sqlManagerSubTabLink);
  }

  /* Form and grid methods */
  /**
   * Get number of backups
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Create new db backup
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async createDbDbBackup(page: Page): Promise<string> {
    await Promise.all([
      page.locator(this.newBackupButton).click(),
      this.waitForVisibleSelector(page, this.tableRow(1)),
      this.waitForVisibleSelector(page, this.downloadBackupButton),
    ]);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Download backup
   * @param page {Page} Browser tab
   * @return {Promise<string|null>}
   */
  downloadDbBackup(page: Page): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.downloadBackupButton);
  }

  /**
   * Delete backup
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<string>}
   */
  async deleteBackup(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.locator(this.dropdownToggleButton(row)).click(),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteRowLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteDbBackups(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteDbBackups(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

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

    await this.confirmDeleteDbBackups(page);

    return this.getAlertSuccessBlockParagraphContent(page);
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
   * @param number {number} Pagination limit number to select
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
}

export default new DbBackup();
