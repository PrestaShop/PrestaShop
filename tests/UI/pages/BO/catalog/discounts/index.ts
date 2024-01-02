import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Cart rules page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CartRules extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewCartRuleButton: string;

  private readonly catalogPriceRulesTab: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnSelectRowCheckbox: (row: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnName: (row: number) => string;

  private readonly tableColumnPriority: (row: number) => string;

  private readonly tableColumnCode: (row: number) => string;

  private readonly tableColumnQuantity: (row: number) => string;

  private readonly tableColumnExpirationDate: (row: number) => string;

  private readonly tableColumnStatusLink: (row: number) => string;

  private readonly tableColumnStatusEnableLink: (row: number) => string;

  private readonly tableColumnStatusDisableLink: (row: number) => string;

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

  private readonly bulkEnableLink: string;

  private readonly bulkDisableLink: string;

  private readonly bulkDeleteLink: string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on cart rules page
   */
  constructor() {
    super();

    this.pageTitle = 'Cart Rules •';

    // Selectors
    this.addNewCartRuleButton = '#page-header-desc-cart_rule-new_cart_rule';
    this.catalogPriceRulesTab = '#subtab-AdminSpecificPriceRule';

    // Form selectors
    this.gridForm = '#form-cart_rule';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-cart_rule';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='cart_ruleFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtoncart_rule';
    this.filterResetButton = 'button[name=\'submitResetcart_rule\']';

    // Table body selectors
    this.tableBodyRows = `${this.gridTable} tbody tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnSelectRowCheckbox = (row: number) => `${this.tableBodyColumn(row)} input[name='cart_ruleBox[]']`;
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnPriority = (row: number) => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnCode = (row: number) => `${this.tableBodyColumn(row)}:nth-child(5)`;
    this.tableColumnQuantity = (row: number) => `${this.tableBodyColumn(row)}:nth-child(6)`;
    this.tableColumnExpirationDate = (row: number) => `${this.tableBodyColumn(row)}:nth-child(7)`;
    this.tableColumnStatusLink = (row: number) => `${this.tableBodyColumn(row)}:nth-child(8) a`;
    this.tableColumnStatusEnableLink = (row: number) => `${this.tableColumnStatusLink(row)}.action-enabled`;
    this.tableColumnStatusDisableLink = (row: number) => `${this.tableColumnStatusLink(row)}.action-disabled`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_cart_rule';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkEnableLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;
    this.bulkDisableLink = `${this.bulkActionDropdownMenu} li:nth-child(5)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(7)`;

    // Pagination selectors
    this.paginationActiveLabel = `${this.gridForm} ul.pagination.pull-right li.active a`;
    this.paginationDiv = `${this.gridForm} .pagination`;
    this.paginationDropdownButton = `${this.paginationDiv} .dropdown-toggle`;
    this.paginationItems = (number: number) => `${this.gridForm} .dropdown-menu a[data-items='${number}']`;
    this.paginationPreviousLink = `${this.gridForm} .icon-angle-left`;
    this.paginationNextLink = `${this.gridForm} .icon-angle-right`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: number) => `${this.tableHead} th:nth-child(${column})`;
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header methods */
  /**
   * Change tab to Catalog Price Rules in Discounts Page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCatalogPriceRulesTab(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.catalogPriceRulesTab);
    await this.waitForVisibleSelector(page, `${this.catalogPriceRulesTab}.current`);
  }

  /**
   * Go to add new cart rule page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCartRulesPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCartRuleButton);
  }

  /* Table methods */
  /**
   * Go to edit cart rule page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<void>}
   */
  async goToEditCartRulePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete cart rule
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param cartRuleName {string} Cart rule name to delete
   * @returns {Promise<string>}
   */
  async deleteCartRule(page: Page, row: number = 1, cartRuleName: string = ''): Promise<string> {
    if (await this.elementVisible(page, this.filterColumn('name'), 1000)) {
      await this.filterCartRules(page, 'input', 'name', cartRuleName);
    }

    await page.locator(this.tableColumnActionsToggleButton(row)).click();

    await this.waitForSelectorAndClick(page, this.tableColumnActionsDeleteLink(row));

    // Confirm delete action
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Get successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param columnName {string} Column name to get text content
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'id_cart_rule':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'priority':
        columnSelector = this.tableColumnPriority(row);
        break;

      case 'code':
        columnSelector = this.tableColumnCode(row);
        break;

      case 'quantity':
        columnSelector = this.tableColumnQuantity(row);
        break;

      case 'date':
        columnSelector = this.tableColumnExpirationDate(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Get cart rule status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getCartRuleStatus(page: Page, row: number): Promise<boolean> {
    return this.elementVisible(page, this.tableColumnStatusEnableLink(row), 1000);
  }

  /**
   * Set cart rule status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<void>}
   */
  async setCartRuleStatus(page: Page, row: number, wantedStatus: boolean): Promise<void> {
    if (wantedStatus !== await this.getCartRuleStatus(page, row)) {
      await page.locator(this.tableColumnStatusLink(row)).click();
    }
  }

  /* Filter methods */
  /**
   * Reset all filters
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }

    // No search button displayed if only one element in table
    await this.waitForVisibleSelector(page, this.gridTableNumberOfTitlesSpan, 2000);
  }

  /**
   * Get number of cart rules
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of cart rules
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter cart rules
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to filter with
   * @return {Promise<void>}
   */
  async filterCartRules(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    await this.resetFilter(page);
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

  /* Bulk actions methods */

  /**
   * Select all rows
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page): Promise<void> {
    await page.locator(this.bulkActionMenuButton).click();

    await Promise.all([
      page.locator(this.selectAllLink).click(),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Bulk delete cart rules
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteCartRules(page: Page): Promise<string> {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await this.bulkSelectRows(page);

    // Perform delete
    await page.locator(this.bulkActionMenuButton).click();
    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Bulk set status
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<void>}
   */
  async bulkSetStatus(page: Page, wantedStatus: boolean): Promise<void> {
    // Select all rows
    await this.bulkSelectRows(page);

    // Set status
    await Promise.all([
      page.locator(this.bulkActionMenuButton).click(),
      this.waitForVisibleSelector(page, this.bulkEnableLink),
    ]);

    await this.clickAndWaitForURL(
      page,
      wantedStatus ? this.bulkEnableLink : this.bulkDisableLink,
    );
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPaginationLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationActiveLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Value of pagination limit to select
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

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, columnName: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumn(page, i, columnName);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector: string;

    switch (sortBy) {
      case 'id_cart_rule':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'priority':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'code':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'quantity':
        columnSelector = this.sortColumnDiv(6);
        break;

      case 'date':
        columnSelector = this.sortColumnDiv(7);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForURL(page, sortColumnButton);
  }
}

export default new CartRules();
