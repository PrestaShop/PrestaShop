import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Add catalog price rule page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class CatalogPriceRules extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewCatalogPriceRuleButton: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterDateFromColumn: (filterBy: string) => string;

  private readonly filterDateToColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: number) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly confirmDeleteButton: string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add catalog price rule page
   */
  constructor() {
    super();

    this.pageTitle = 'Catalog Price Rules •';

    // Selectors header
    this.addNewCatalogPriceRuleButton = 'a[data-role=page-header-desc-specific_price_rule-link]';

    // Form selectors
    this.gridForm = '#form-specific_price_rule';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Selectors grid panel
    this.gridPanel = '#attachment_grid_panel';
    this.gridTable = '#table-specific_price_rule';

    // Filters
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='specific_price_ruleFilter_${filterBy}']`;
    this.filterDateFromColumn = (filterBy: string) => `${this.filterRow} #local_specific_price_ruleFilter_a__${filterBy}_0`;
    this.filterDateToColumn = (filterBy: string) => `${this.filterRow} #local_specific_price_ruleFilter_a__${filterBy}_1`;
    this.filterSearchButton = `${this.gridTable} #submitFilterButtonspecific_price_rule`;
    this.filterResetButton = 'button[name=\'submitResetspecific_price_rule\']';

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: number) => `${this.tableRow(row)} td:nth-child(${column})`;

    // Actions buttons in Row
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td .btn-group-action`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} button.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} ul.dropdown-menu`;
    this.confirmDeleteButton = '#popup_ok';
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.delete`;
    this.editRowLink = (row: number) => `${this.actionsColumn(row)} a.edit`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_specific_price_rule';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

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
  }

  /* Methods */
  /**
   * Go to add new Catalog price rule page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCatalogPriceRulePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCatalogPriceRuleButton);
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
   * Get number of catalog price rules
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
  }

  /**
   * Reset and get number of catalog price rules
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.resetFilter(page);
    }
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Go to edit catalog price rule page
   * @param page {Page} Browser tab
   * @param ruleName {string} Value of rule name to edit
   * @returns {Promise<void>}
   */
  async goToEditCatalogPriceRulePage(page: Page, ruleName: string): Promise<void> {
    if (await this.elementVisible(page, this.filterColumn('a!name'))) {
      await this.filterPriceRules(page, 'select', 'a!name', ruleName);
    }
    await this.clickAndWaitForURL(page, this.editRowLink(1));
  }

  /**
   * Filter catalog price rules table
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value  {string} Value to put on filter
   * @returns {Promise<void>}
   */
  async filterPriceRules(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await Promise.all([
          page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
          this.selectByVisibleText(page, this.filterColumn(filterBy), value),
        ]);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
  }

  /**
   * Filter by date
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter
   * @param dateFrom {string} Value of 'date from' to set on input
   * @param dateTo {string} Value of 'date to' to set on input
   * @returns {Promise<void>}
   */
  async filterByDate(page: Page, filterBy: string, dateFrom: string, dateTo: string): Promise<void> {
    await page.locator(this.filterDateFromColumn(filterBy)).fill(dateFrom);
    await page.locator(this.filterDateToColumn(filterBy)).fill(dateTo);
    // click on search
    await page.click(this.filterSearchButton);
    await this.elementVisible(page, this.filterResetButton);
  }

  /**
   * Delete catalog price rule
   * @param page {Page} Browser tab
   * @param ruleName {string} Value of rule name to delete
   * @returns {Promise<string>}
   */
  async deleteCatalogPriceRule(page: Page, ruleName: string): Promise<string> {
    if (await this.elementVisible(page, this.filterColumn('a!name'))) {
      await this.filterPriceRules(page, 'select', 'a!name', ruleName);
    }
    await this.waitForSelectorAndClick(page, this.dropdownToggleButton(1));
    await Promise.all([
      page.click(this.deleteRowLink(1)),
      this.waitForVisibleSelector(page, this.confirmDeleteButton),
    ]);
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);

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
      case 'id_specific_price_rule':
        columnSelector = this.tableColumn(row, 2);
        break;

      case 'a!name':
        columnSelector = this.tableColumn(row, 3);
        break;

      case 'cul!name':
        columnSelector = this.tableColumn(row, 4);
        break;

      case 'cl!name':
        columnSelector = this.tableColumn(row, 5);
        break;

      case 'gl!name':
        columnSelector = this.tableColumn(row, 6);
        break;

      case 'from_quantity':
        columnSelector = this.tableColumn(row, 7);
        break;

      case 'a!reduction_type':
        columnSelector = this.tableColumn(row, 8);
        break;

      case 'reduction':
        columnSelector = this.tableColumn(row, 9);
        break;

      case 'from':
        columnSelector = this.tableColumn(row, 10);
        break;

      case 'to':
        columnSelector = this.tableColumn(row, 11);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Select all rows
   * @param page
   * @return {Promise<void>}
   */
  async bulkSelectRows(page: Page): Promise<void> {
    await page.click(this.bulkActionMenuButton);

    await Promise.all([
      page.click(this.selectAllLink),
      this.waitForHiddenSelector(page, this.selectAllLink),
    ]);
  }

  /**
   * Bulk delete price rules
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeletePriceRules(page: Page): Promise<string> {
    // To confirm bulk delete action with dialog
    await this.dialogListener(page, true);

    // Select all rows
    await this.bulkSelectRows(page);

    // Perform delete
    await page.click(this.bulkActionMenuButton);
    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName {string} Column name to get all rows column
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
   * Sort table
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector: string;

    switch (sortBy) {
      case 'id_specific_price_rule':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'a!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'cul!name':
        columnSelector = this.sortColumnDiv(4);
        break;

      case 'cl!name':
        columnSelector = this.sortColumnDiv(5);
        break;

      case 'gl!name':
        columnSelector = this.sortColumnDiv(6);
        break;

      case 'from_quantity':
        columnSelector = this.sortColumnDiv(7);
        break;

      case 'a!reduction_type':
        columnSelector = this.sortColumnDiv(8);
        break;

      case 'reduction':
        columnSelector = this.sortColumnDiv(9);
        break;

      case 'from':
        columnSelector = this.sortColumnDiv(10);
        break;

      case 'to':
        columnSelector = this.sortColumnDiv(11);
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
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.paginationDropdownButton);
    await this.clickAndWaitForURL(page, this.paginationItems(number));

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

export default new CatalogPriceRules();
