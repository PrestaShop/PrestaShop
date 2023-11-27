import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Multistore page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class MultiStoreSettings extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newShopGroupLink: string;

  private readonly newShopLink: string;

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

  private readonly listTableColumn: (row: number, column: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes: string;

  private readonly multistoreTree: string;

  private readonly shopLink: (id: number) => string;

  private readonly shopUrlLink: (id: number) => string;

  private readonly shopGroupLink: (id: number) => string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly defaultShopSelect: string;

  private readonly saveButton: string;

  private readonly sortColumnDiv: (number: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on multistore page
   */
  constructor() {
    super();

    this.pageTitle = 'Multistore • ';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    this.alertSuccessBlockParagraph = '.alert-success';

    // Header selectors
    this.newShopGroupLink = '#page-header-desc-shop_group-new';
    this.newShopLink = '#page-header-desc-shop_group-new_2';

    // Form selectors
    this.gridForm = '#form-shop_group';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-shop_group';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='shop_groupFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonshop_group';
    this.filterResetButton = 'button[name=\'submitResetshop_group\']';

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;
    this.listTableColumn = (row: number, column: number) => `${this.tableBodyColumn(row)}:nth-child(${column})`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Multistore tree selectors
    this.multistoreTree = '#shops-tree';
    this.shopLink = (id: number) => `${this.multistoreTree} a[href*='shop_id=${id}']`;
    this.shopUrlLink = (id: number) => `${this.multistoreTree} a[href*='shop_url=${id}']`;
    this.shopGroupLink = (id: number) => `${this.multistoreTree} a[href*='id_shop_group=${id}']`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = (number: number) => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;

    // Multistore options selectors
    this.defaultShopSelect = '#PS_SHOP_DEFAULT';
    this.saveButton = '#shop_group_fieldset_general div.panel-footer button[name="submitOptionsshop_group"]';
  }

  /* Header methods */
  /**
   * Go to new shop group page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewShopGroupPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newShopGroupLink);
  }

  /**
   * Go to new shop page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToNewShopPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newShopLink);
  }

  /* Filter methods */

  /**
   * Get number of elements
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
  }

  /**
   * Reset and get number of shop groups
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter shop groups
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterBy: string, value: string): Promise<void> {
    await this.setValue(page, this.filterColumn(filterBy), value);
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Go to edit shop group page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditShopGroupPage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete shop group from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteShopGroup(page: Page, row: number): Promise<string> {
    await Promise.all([
      page.locator(this.tableColumnActionsToggleButton(row)).click(),
      this.waitForVisibleSelector(page, this.tableColumnActionsDeleteLink(row)),
    ]);

    await page.locator(this.tableColumnActionsDeleteLink(row)).click();

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is action toggle button visible
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<boolean>}
   */
  async isActionToggleButtonVisible(page: Page, row: number): Promise<boolean> {
    return this.elementVisible(page, this.tableColumnActionsToggleButton(row));
  }

  /**
   * Go to shop link
   * @param page {Page} Browser tab
   * @param id {number} Row on table
   * @returns {Promise<void>}
   */
  async goToShopPage(page: Page, id: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.shopLink(id));
  }

  /**
   * Go to shop url link
   * @param page {Page} Browser tab
   * @param id {number} Row on table
   * @returns {Promise<void>}
   */
  async goToShopURLPage(page: Page, id: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.shopUrlLink(id));
  }

  /**
   * Go to shop url link
   * @param page {Page} Browser tab
   * @param id {number} Id of the shop group
   * @returns {Promise<void>}
   */
  async goToShopGroupPage(page: Page, id: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.shopGroupLink(id));
  }

  /**
   * Get text from a column from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column name to get text content
   * @returns {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, column: string): Promise<string> {
    let columnSelector: string;

    switch (column) {
      case 'id_shop_group':
        columnSelector = this.listTableColumn(row, 1);
        break;

      case 'a!name':
        columnSelector = this.listTableColumn(row, 2);
        break;

      default:
        throw new Error(`Column ${column} was not found`);
    }

    return this.getTextContent(page, columnSelector);
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
   * @param number {number} Value of pagination
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await page.locator(this.paginationItems(number)).click();

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
    let columnSelector: string;

    switch (sortBy) {
      case 'id_shop_group':
        columnSelector = this.sortColumnDiv(1);
        break;

      case 'a!name':
        columnSelector = this.sortColumnDiv(2);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForURL(page, sortColumnButton);
  }

  // Multistore options methods

  /**
   * Select default store
   * @param page {Page} Browser tab
   * @param defaultStore {string} Default store name to select
   * @return {Promise<string>}
   */
  async selectDefaultStore(page: Page, defaultStore: string): Promise<string> {
    await this.selectByVisibleText(page, this.defaultShopSelect, defaultStore);
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new MultiStoreSettings();
