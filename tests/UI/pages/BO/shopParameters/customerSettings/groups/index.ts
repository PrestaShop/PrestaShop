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

  private readonly tableBodyColumns: (row: number, column: string) => string;

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

  private readonly paginationDiv: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationListOpen: string;

  private readonly paginationNumber: (number: number) => string;

  private readonly paginationRightBlock: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string, direction: string) => string;

  private readonly visitorsGroupSelect: string;

  private readonly guestsGroupSelect: string;

  private readonly customersGroupSelect: string;

  private readonly saveButton: string;

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
    this.tableBodyColumns = (row: number, column: string) => `${this.tableBodyRow(row)} td.column-${column}`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyRow(row)} .btn-group-action`;
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

    // Pagination selectors
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationLimitSelect = `${this.paginationDiv}  button.dropdown-toggle`;
    this.paginationListOpen = `${this.paginationDiv}.open`;
    this.paginationNumber = (number: number) => `${this.gridForm} div.row li a[data-items='${number}']`;
    this.paginationRightBlock = `${this.paginationDiv}.pull-right`;
    this.paginationLabel = `${this.paginationRightBlock} li.active a`;
    this.paginationNextLink = `${this.paginationRightBlock} i.icon-angle-right`;
    this.paginationPreviousLink = `${this.paginationRightBlock} i.icon-angle-left`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string, direction: string) => `${this.tableHead} a.${direction}-sort-column-${column}-link`;

    // Default groups options selectors
    this.visitorsGroupSelect = '#PS_UNIDENTIFIED_GROUP';
    this.guestsGroupSelect = '#PS_GUEST_GROUP';
    this.customersGroupSelect = '#PS_CUSTOMER_GROUP';
    this.saveButton = '#group_fieldset_general div.panel-footer button[type="submit"]';
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
   * @param value {string |number} Value to filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string | number): Promise<void> {
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

  /* Column methods */

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    if (columnName === 'b!name') {
      // eslint-disable-next-line no-param-reassign
      columnName = 'name';
    }
    return this.getTextContent(page, this.tableBodyColumns(row, columnName));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
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
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await page.locator(this.paginationLimitSelect).click();
    await this.waitForVisibleSelector(page, this.paginationListOpen);
    await this.clickAndWaitForURL(page, this.paginationNumber(number));

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

  /* Sort methods */
  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    await this.clickAndWaitForURL(page, `${this.sortColumnDiv(sortBy, sortDirection)} i`);
  }

  /* Default groups options selectors */
  /**
   * Get group selected option
   * @param page {Page} Browser tab
   * @param group {string} Group to get selected value
   * @return {Promise<string>}
   */
  async getGroupSelectedValue(page: Page, group: string): Promise<string> {
    switch (group) {
      case 'visitors':
        return this.getTextContent(page, `${this.visitorsGroupSelect} option[selected]`, false);
      case 'guests':
        return this.getTextContent(page, `${this.guestsGroupSelect} option[selected]`, false);
      case 'customers':
        return this.getTextContent(page, `${this.customersGroupSelect} option[selected]`, false);
      default:
        throw new Error(`Group ${group} was not found`);
    }
  }

  /**
   * Get group dropdown list
   * @param page {Page} Browser tab
   * @param group {string} Group to get dropdown list
   * @return {Promise<string>}
   */
  async getGroupDropDownList(page: Page, group: string): Promise<string> {
    switch (group) {
      case 'visitors':
        return this.getTextContent(page, this.visitorsGroupSelect);
      case 'guests':
        return this.getTextContent(page, this.guestsGroupSelect);
      case 'customers':
        return this.getTextContent(page, this.customersGroupSelect);
      default:
        throw new Error(`Group ${group} was not found`);
    }
  }
}

export default new Groups();
