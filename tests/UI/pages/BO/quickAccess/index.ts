import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Quick access page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class QuickAccess extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewQuickAccessButton: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnName: (row: number) => string;

  private readonly tableColumnLink: (row: number) => string;

  private readonly tableColumnIsNewWindow: (row: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on quick access page
   */
  constructor() {
    super();

    this.pageTitle = 'Quick Access •';

    // Selectors
    // Header selectors
    this.addNewQuickAccessButton = 'a[data-role=page-header-desc-quick_access-link]';

    // Table selectors
    this.gridTable = '#table-quick_access';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='quick_accessFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonquick_access';
    this.filterResetButton = 'button[name=\'submitResetquick_access\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnLink = (row: number) => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnIsNewWindow = (row: number) => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_quick_access';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /*
  Methods
   */

  /**
   * Go to add new quick access page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewQuickAccessPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewQuickAccessButton);
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param columnName {string} Column name in the table
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'id_quick_access':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'link':
        columnSelector = this.tableColumnLink(row);
        break;

      case 'new_window':
        columnSelector = this.tableColumnIsNewWindow(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Filter quick access table
   * @param page {Page} Browser tab
   * @param filterType {string} Type of the filter (input or select)
   * @param filterBy {string} Value to use for the select type filter
   * @param value {string|number} Value for the select filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy:string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Bulk delete link
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteQuickAccessLink(page: Page): Promise<string> {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.locator(this.bulkActionMenuButton).click(),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.locator(this.selectAllLink).click(),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.locator(this.bulkActionMenuButton).click(),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }
}

export default new QuickAccess();
