import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Groups page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Groups extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newGroupLink: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfGroupsSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumns: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes: string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on groups page
   */
  constructor() {
    super();

    this.pageTitle = 'Groups •';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newGroupLink = 'a[data-role=page-header-desc-group-link]';

    // Form selectors
    this.gridForm = '#form-group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfGroupsSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtongroup';
    this.filterResetButton = 'button[name=\'submitResetgroup\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumns = (row: number) => `${this.tableBodyRow(row)} td`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumns(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_group';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
  }

  /* Header methods */
  /**
   * Go to new group page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewGroupPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newGroupLink);
  }

  /* Filter methods */

  /**
   * Get number of groups
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfGroupsSpan);
  }

  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForURL(page, this.filterResetButton);
    }
    await this.waitForVisibleSelector(page, this.filterSearchButton, 2000);
  }

  /**
   * Reset and get number of groups
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter groups
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter field( input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
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

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    // Get all text columns
    const textColumns = await page.$$eval(
      `${this.tableBodyColumns(row)}:not(.row-selector)`,
      (els: HTMLElement[]) => els.map((el: HTMLElement) => (el.textContent ?? '').trim()),
    );

    switch (columnName) {
      case 'id_group':
        return textColumns[0];

      case 'b!name':
        return textColumns[1];

      case 'reduction':
        return textColumns[2];

      case 'nb':
        return textColumns[3];

      case 'show_prices':
        return textColumns[4];

      default:
        throw new Error(`Column ${columnName} was not found`);
    }
  }

  /**
   * Go to edit group page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditGroupPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete group from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteGroup(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Bulk actions methods */

  /**
   * Bulk delete groups
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteGroups(page: Page): Promise<string> {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.selectAllLink),
    ]);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);

    // Perform delete
    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Groups();
