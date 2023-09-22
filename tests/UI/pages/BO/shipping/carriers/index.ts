import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Carriers page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Carriers extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly addNewCarrierLink: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

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

  private readonly tableColumnDelay: (row: number) => string;

  private readonly tableColumnActive: (row: number) => string;

  private readonly enableColumnValidIcon: (row: number) => string;

  private readonly tableColumnIsFree: (row: number) => string;

  private readonly tableColumnPosition: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly deleteModalButtonYes: string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkEnableLink: string;

  private readonly bulkDisableLink: string;

  private readonly bulkDeleteLink: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on carriers page
   */
  constructor() {
    super();

    this.pageTitle = 'Carriers •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors
    this.growlMessageBlock = '#growls .growl-message:last-of-type';

    // Header links
    this.addNewCarrierLink = 'a[data-role=page-header-desc-carrier-link]';

    // Form selectors
    this.gridForm = '#form-carrier';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-carrier';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='carrierFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncarrier';
    this.filterResetButton = 'button[name=\'submitResetcarrier\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnDelay = (row: number) => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnActive = (row: number) => `${this.tableBodyColumn(row)}:nth-child(6) a`;
    this.enableColumnValidIcon = (row: number) => `${this.tableColumnActive(row)} i.icon-check`;
    this.tableColumnIsFree = (row: number) => `${this.tableBodyColumn(row)}:nth-child(7) a`;
    this.tableColumnPosition = (row: number) => `${this.tableBodyColumn(row)}:nth-child(8)`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = (number: number) => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_carrier';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;
  }

  /*
  Methods
   */
  /**
   * Go to add new carrier page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCarrierPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCarrierLink);
  }

  /* Filter methods */

  /**
   * Get Number of carriers
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
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
   * Reset and get number of image types
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter carriers table
   * @param page {Page} Browser tab
   * @param filterType {string} Type of the filter (input or select)
   * @param filterBy {string} Value to use for the select type filter
   * @param value {string|number} Value for the select filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value === '1' ? 'Yes' : 'No'),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /* Column methods */

  /**
   * Go to edit carrier page
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @return {Promise<void>}
   */
  async gotoEditCarrierPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param columnName {string} Column name in the table
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector;

    switch (columnName) {
      case 'id_carrier':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'delay':
        columnSelector = this.tableColumnDelay(row);
        break;

      case 'active':
        columnSelector = this.tableColumnActive(row);
        break;

      case 'is_free':
        columnSelector = this.tableColumnIsFree(row);
        break;

      case 'a!position':
        columnSelector = this.tableColumnPosition(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    if ((columnName === 'active') || (columnName === 'is_free')) {
      return this.getAttributeContent(page, columnSelector, 'title');
    }
    return this.getTextContent(page, columnSelector);
  }

  /**
   * Delete carrier from row
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @return {Promise<string>}
   */
  async deleteCarrier(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.click(this.tableColumnActionsToggleButton(row)),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.click(this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name in the table
   * @return {Promise<string[]>}
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
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} column to sort with
   * @param sortDirection {string} asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector;

    switch (sortBy) {
      case 'id_carrier':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!position':
        columnSelector = this.sortColumnDiv(8);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForURL(page, sortColumnButton);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {Number} The pagination number value
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.waitForSelectorAndClick(page, this.paginationItems(number));
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

  /* Bulk actions methods */
  /**
   * Bulk delete carriers
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteCarriers(page: Page): Promise<string> {
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
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set carriers status
   * @param page {Page} Browser tab
   * @param action {string} The action to perform in bulk
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, action: string): Promise<string> {
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

    if (action === 'Enable') {
      await this.clickAndWaitForURL(page, this.bulkEnableLink);
    } else {
      await this.clickAndWaitForURL(page, this.bulkDisableLink);
    }

    // Return successful message
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get carrier status
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @returns {Promise<boolean>}
   */
  async getStatus(page: Page, row: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.enableColumnValidIcon(row), 100);
  }

  /**
   * Set carriers status
   * @param page {Page} Browser tab
   * @param row {number} Row index in the table
   * @param valueWanted {boolean} The carrier status value
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page: Page, row: number = 1, valueWanted: boolean = true): Promise<boolean> {
    await this.waitForVisibleSelector(page, this.tableColumnActive(row), 2000);

    if (await this.getStatus(page, row) !== valueWanted) {
      await page.click(this.tableColumnActive(row));
      return true;
    }

    return false;
  }

  /**
   * Change carrier position
   * @param page {Page} Browser tab
   * @param actualPosition {number} The actual row position
   * @param newPosition {number} The new position for the row
   * @return {Promise<string>}
   */
  async changePosition(page: Page, actualPosition: number, newPosition: number): Promise<string | null> {
    await this.dragAndDrop(
      page,
      this.tableColumnPosition(actualPosition),
      this.tableColumnPosition(newPosition),
    );

    return this.getGrowlMessageContent(page);
  }
}

export default new Carriers();
