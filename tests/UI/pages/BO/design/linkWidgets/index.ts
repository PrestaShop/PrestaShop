import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Link list page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class LinkList extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newBlockLink: string;

  private readonly gridPanel: (hookName: string) => string;

  private readonly gridHeaderTitle: (hookName: string) => string;

  private readonly gridTable: (hookName: string) => string;

  private readonly tableRow: (hookName: string, row: number) => string;

  private readonly tableColumn: (hookName: string, row: number, column: string) => string;

  private readonly actionsColumn: (hookName: string, row: number) => string;

  private readonly dropdownToggleButton: (hookName: string, row: number) => string;

  private readonly dropdownToggleMenu: (hookName: string, row: number) => string;

  private readonly deleteRowLink: (hookName: string, row: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on link list page
   */
  constructor() {
    super();

    this.pageTitle = 'Link List •';

    // Successful Messages
    this.successfulCreationMessage = 'Successful creation.';
    this.successfulDeleteMessage = 'Successful deletion.';

    // Header Selectors
    this.newBlockLink = '#page-header-desc-configuration-add';
    this.gridPanel = (hookName: string) => `div[data-hook-name='${hookName}']`;
    this.gridHeaderTitle = (hookName: string) => `${this.gridPanel(hookName)} h3.card-header-title`;
    this.gridTable = (hookName: string) => `${this.gridPanel(hookName)} table.grid-table`;
    this.tableRow = (hookName: string, row: number) => `${this.gridTable(hookName)} tbody tr:nth-child(${row})`;
    this.tableColumn = (hookName: string, row: number, column: string) => `${this.tableRow(hookName, row)} td.column-${column}`;
    this.actionsColumn = (hookName: string, row: number) => `${this.tableRow(hookName, row)} td.column-actions`;
    this.dropdownToggleButton = (hookName: string, row: number) => `${this.actionsColumn(hookName, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (hookName: string, row: number) => `${this.actionsColumn(hookName, row)} div.dropdown-menu`;
    this.deleteRowLink = (hookName: string, row: number) => `${this.dropdownToggleMenu(hookName, row)} a.grid-delete-row-link`;
  }

  /* Header methods */
  /**
   * Go to new Block page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewLinkWidgetPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newBlockLink);
  }

  /* Table methods */
  /**
   * Get number of element in grid
   * @param page {Page} Browser tab
   * @param hookName {string} Table name to get number of elements
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page, hookName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle(hookName));
  }

  /**
   * Delete link widget
   * @param page {Page} Browser tab
   * @param hookName {string} Table name to delete from
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteLinkWidget(page: Page, hookName: string, row: number): Promise<string> {
    await this.dialogListener(page, true);
    await Promise.all([
      page.click(this.dropdownToggleButton(hookName, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(hookName, row)}[aria-expanded='true']`),
    ]);
    await page.click(this.deleteRowLink(hookName, row));

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new LinkList();
