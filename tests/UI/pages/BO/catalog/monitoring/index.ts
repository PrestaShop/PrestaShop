import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Monitoring page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Monitoring extends BOBasePage {
  public readonly pageTitle: string;

  private readonly gridPanel: (table: string) => string;

  private readonly gridTable: (table: string) => string;

  private readonly gridHeaderTitle: (table: string) => string;

  private readonly bulkActionsButton: (table: string) => string;

  private readonly deleteSelectButton: (table: string) => string;

  private readonly selectAllCheckBox: (table: string) => string;

  private readonly filterColumn: (table: string, filterBy: string) => string;

  private readonly filterSearchButton: (table: string) => string;

  private readonly filterResetButton: (table: string) => string;

  private readonly tableBody: (table: string) => string;

  private readonly tableRow: (table: string, row: number) => string;

  private readonly tableEmptyRow: (table: string) => string;

  private readonly tableColumn: (table: string, row: number, column: string) => string;

  private readonly enableColumn: (table: string, row: number) => string;

  private readonly enableColumnValidIcon: (table: string, row: number) => string;

  private readonly actionsColumn: (table: string, row: number) => string;

  private readonly editRowLink: (table: string, row: number) => string;

  private readonly dropdownToggleButton: (table: string, row: number) => string;

  private readonly dropdownToggleMenu: (table: string, row: number) => string;

  private readonly deleteRowLink: (table: string, row: number) => string;

  private readonly viewCategoryRowLink: (row: number) => string;

  private readonly editCategoryRowLink: (row: number) => string;

  private readonly deleteCategoryRowLink: (row: number) => string;

  private readonly deleteModeCategoryModal: string;

  private readonly deleteModeInput: (position: number) => string;

  private readonly deleteModeCategoryModalDiv: string;

  private readonly submitDeleteCategoryButton: string;

  private readonly tableHead: (table: string) => string;

  private readonly sortColumnDiv: (table: string, column: string) => string;

  private readonly sortColumnSpanButton: (table: string, column: string) => string;

  private readonly deleteProductModal: (table: string) => string;

  private readonly submitDeleteProductButton: (table: string) => string;

  private readonly paginationLimitSelect: (table: string) => string;

  private readonly paginationLabel: (table: string) => string;

  private readonly paginationNextLink: (table: string) => string;

  private readonly paginationPreviousLink: (table: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on monitoring page
   */
  constructor() {
    super();

    this.pageTitle = 'Monitoring •';

    // Selectors
    this.gridPanel = (table: string) => `#${table}_grid_panel`;
    this.gridTable = (table: string) => `#${table}_grid_table`;
    this.gridHeaderTitle = (table: string) => `${this.gridPanel(table)} div.card-header h3`;

    // Bulk actions
    this.bulkActionsButton = (table: string) => `${this.gridPanel(table)} .js-bulk-actions-btn`;
    this.deleteSelectButton = (table: string) => `#${table}_grid_bulk_action_delete_selection`;

    // Filters
    this.selectAllCheckBox = (table: string) => `${this.gridPanel(table)} .js-bulk-action-select-all`;
    this.filterColumn = (table: string, filterBy: string) => `${this.gridTable(table)} #${table}_${filterBy}`;
    this.filterSearchButton = (table: string) => `${this.gridTable(table)} .grid-search-button`;
    this.filterResetButton = (table: string) => `${this.gridTable(table)} .grid-reset-button`;

    // Table
    this.tableBody = (table: string) => `${this.gridTable(table)} tbody`;
    this.tableRow = (table: string, row: number) => `${this.tableBody(table)} tr:nth-child(${row})`;
    this.tableEmptyRow = (table: string) => `${this.tableBody(table)} tr.empty_row`;
    this.tableColumn = (table: string, row: number, column: string) => `${this.tableRow(table, row)} td.column-${column}`;

    // Enable column
    this.enableColumn = (table: string, row: number) => this.tableColumn(table, row, 'active');
    this.enableColumnValidIcon = (table: string, row: number) => `${this.enableColumn(table, row)} i.grid-toggler-icon-valid`;

    // Actions buttons in Row
    this.actionsColumn = (table: string, row: number) => `${this.tableRow(table, row)} td.column-actions`;
    this.editRowLink = (table: string, row: number) => `${this.actionsColumn(table, row)} a.grid-edit-row-link`;
    this.dropdownToggleButton = (table: string, row: number) => `${this.actionsColumn(table, row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (table: string, row: number) => `${this.actionsColumn(table, row)} div.dropdown-menu`;
    this.deleteRowLink = (table: string, row: number) => `${this.dropdownToggleMenu(table, row)} a.grid-delete-row-link`;

    // Category selectors
    this.viewCategoryRowLink = (row: number) => `${this.actionsColumn('empty_category', row)} a.grid-view-row-link`;
    this.editCategoryRowLink = (row: number) => `${this.dropdownToggleMenu('empty_category', row)} a.grid-edit-row-link`;
    this.deleteCategoryRowLink = (row: number) => `${this.dropdownToggleMenu('empty_category', row)
    } a.grid-delete-row-link`;
    this.deleteModeCategoryModal = '#empty_category_grid_delete_categories_modal';
    this.deleteModeInput = (position: number) => `#delete_categories_delete_mode_${position} + i`;
    this.deleteModeCategoryModalDiv = '#delete_categories_delete_mode';
    this.submitDeleteCategoryButton = `${this.deleteModeCategoryModal} button.js-submit-delete-categories`;

    // Sort Selectors
    this.tableHead = (table: string) => `${this.gridTable(table)} thead`;
    this.sortColumnDiv = (table: string, column: string) => `${this.tableHead(table)
    } div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (table: string, column: string) => `${this.sortColumnDiv(table, column)} span.ps-sort`;

    // Modal products list
    this.deleteProductModal = (table: string) => `#${table}-grid-confirm-modal`;
    this.submitDeleteProductButton = (table: string) => `${this.deleteProductModal(table)} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationLimitSelect = (table: string) => `${this.gridPanel(table)} #paginator_select_page_limit`;
    this.paginationLabel = (table: string) => `${this.gridPanel(table)} .col-form-label`;
    this.paginationNextLink = (table: string) => `${this.gridPanel(table)} [data-role=next-page-link]`;
    this.paginationPreviousLink = (table: string) => `${this.gridPanel(table)} [data-role='previous-page-link']`;
  }

  /* Reset Methods */
  /**
   * Get number of element in table grid
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get number of element from
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page, tableName: string): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle(tableName));
  }

  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset filter
   * @return {Promise<void>}
   */
  async resetFilter(page: Page, tableName: string): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton(tableName), 2000)) {
      await page.click(this.filterResetButton(tableName));
      await this.elementNotVisible(page, this.filterResetButton(tableName), 2000);
    }
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to reset and get number of elements
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page, tableName: string): Promise<number> {
    await this.resetFilter(page, tableName);
    return this.getNumberOfElementInGrid(page, tableName);
  }

  /* Filter Methods */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param tableName {string} table name to filter
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, tableName: string, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(tableName, filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(tableName, filterBy), value ? 'Yes' : 'No');
        break;
      default:
        throw new Error(`Filter column not found : ${filterBy}`);
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton(tableName));
  }

  /* table methods */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get text from
   * @param row {number} Row on table
   * @param column {string} Column name to get text content
   * @return {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, tableName: string, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(tableName, row, column));
  }

  /**
   * Open dropdown menu in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to open dropdown menu
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async openDropdownMenu(page: Page, tableName: string, row: number): Promise<void> {
    await Promise.all([
      page.click(this.dropdownToggleButton(tableName, row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(tableName, row)}[aria-expanded='true']`),
    ]);
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete product from it
   * @param row {number} Row on table to delete
   * @return {Promise<string>}
   */
  async deleteProductInGrid(page: Page, tableName: string, row: number): Promise<string> {
    await this.openDropdownMenu(page, tableName, row);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(tableName, row)),
      this.waitForVisibleSelector(page, `${this.deleteProductModal(tableName)}.show`),
    ]);

    await this.clickAndWaitForURL(page, this.submitDeleteProductButton(tableName));
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Bulk delete elements in table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete elements from it
   * @returns {Promise<string>}
   */
  async bulkDeleteElementsInTable(page: Page, tableName: string): Promise<string> {
    // Select all elements in table
    await Promise.all([
      page.$eval(this.selectAllCheckBox(tableName), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsButton(tableName)}:not([disabled])`),
    ]);

    // Click on bulk actions
    await Promise.all([
      page.click(this.bulkActionsButton(tableName)),
      this.waitForVisibleSelector(page, this.deleteSelectButton(tableName)),
    ]);

    // Click on delete selected and wait for modal
    await Promise.all([
      page.$eval(this.deleteSelectButton(tableName), (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.deleteProductModal(tableName)}.show`),
    ]);

    await page.click(this.submitDeleteProductButton(tableName));

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Categories methods */

  /**
   * Delete Row in table empty categories
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to delete category
   * @param row {number} Row on table to delete
   * @param deletionModePosition {number} value of mode position to delete
   * @return {Promise<string>}
   */
  async deleteCategoryInGrid(page: Page, tableName: string, row: number, deletionModePosition: number) {
    await this.dialogListener(page, true);
    await this.openDropdownMenu(page, tableName, row);
    await Promise.all([
      page.click(this.deleteCategoryRowLink(row)),
      this.waitForVisibleSelector(page, this.deleteModeCategoryModal),
    ]);

    // choose deletion mode
    await page.click(this.deleteModeInput(deletionModePosition));
    await this.clickAndWaitForURL(page, this.submitDeleteCategoryButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get status
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get status
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, tableName: string, row: number = 1): Promise<boolean> {
    return this.elementVisible(page, this.enableColumnValidIcon(tableName, row), 100);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get all rows column content
   * @param column {string} Column name to get text column
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, tableName: string, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page, tableName);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(page, this.tableColumn(tableName, i, column));

      if (column === 'active') {
        rowContent = (await this.getStatus(page, tableName, i)).toString();
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Sort table
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to sort
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, tableName: string, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(tableName, sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(tableName, sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  // Methods for pagination
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to get pagination label
   * @return {Promise<string>}
   */
  getPaginationLabel(page: Page, tableName: string): Promise<string> {
    return this.getTextContent(page, this.paginationLabel(tableName));
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination
   * @param number {number} Value of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, tableName: string, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect(tableName), number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination next
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page, tableName: string): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationNextLink(tableName));

    return this.getPaginationLabel(page, tableName);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @param tableName {string} Table name to select pagination previous
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page, tableName: string): Promise<string> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink(tableName));
    return this.getPaginationLabel(page, tableName);
  }
}

export default new Monitoring();
