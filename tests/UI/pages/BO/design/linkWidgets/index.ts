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

  private readonly tableHead: (hookName: string) => string;

  private readonly sortColumnDiv: (column: string, hookName: string) => string;

  private readonly sortColumnSpanButton: (column: string, hookName: string) => string;

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

    // Sort Selectors
    this.tableHead = (hookName: string) => `${this.gridTable(hookName)} thead`;
    this.sortColumnDiv = (column: string, hookName: string) => `${this.tableHead(hookName)}`
      + ` div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string, hookName: string) => `${this.sortColumnDiv(column, hookName)} span.ps-sort`;
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
      page.locator(this.dropdownToggleButton(hookName, row)).click(),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(hookName, row)}[aria-expanded='true']`),
    ]);
    await page.locator(this.deleteRowLink(hookName, row)).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Sort link widget table
   * @param page {Page} Browser tab
   * @param sortBy{string} column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @param hookName {string} Table name
   * @return {Promise<void>}
   */
  async sortLinkWidget(page: Page, sortBy: string, sortDirection: string, hookName: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy, hookName)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy, hookName);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.locator(sortColumnSpanButton).click();
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Get text column from table
   * @param page {Page} Browser tab
   * @param row {number} Row in table
   * @param columnName {string} Column name to get
   * @param sortColumnName {string} Sorted Column name
   * @param hookName {string} Table name
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string, sortColumnName: string, hookName: string): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'id_link_block':
        columnSelector = this.tableColumn(hookName, row, columnName);
        break;

      case 'block_name':
        columnSelector = this.tableColumn(hookName, row, columnName);
        break;

      case 'position':
        columnSelector = this.tableColumn(hookName, row, columnName);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all content
   * @param sortColumnName {string} Column name to sort
   *  @param hookName {string} Table name
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string, sortColumnName: string, hookName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page, hookName);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName, sortColumnName, hookName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }
}

export default new LinkList();
