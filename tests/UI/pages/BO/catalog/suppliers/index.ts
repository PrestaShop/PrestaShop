import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Suppliers page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Suppliers extends BOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  private readonly newSupplierLink: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly viewRowLink: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly statusColumn: (row: number) => string;

  private readonly statusColumnToggleInput: (row: number) => string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on suppliers page
   */
  constructor() {
    super();

    this.pageTitle = 'Suppliers • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Selectors header
    this.newSupplierLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#supplier_grid_panel';
    this.gridTable = '#supplier_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .grid_bulk_action_select_all`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_enable_selection`;
    this.bulkActionsDisableButton = `${this.gridPanel} #supplier_grid_bulk_action_suppliers_disable_selection`;
    this.bulkActionsDeleteButton = `${this.gridPanel} #supplier_grid_bulk_action_delete_selection`;
    this.confirmDeleteModal = '#supplier-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #supplier_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;

    // Actions buttons in Row
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.viewRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-view-row-link`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.editRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-edit-row-link`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;

    // Column status
    this.statusColumn = (row: number) => `${this.tableColumn(row, 'active')} .ps-switch`;
    this.statusColumnToggleInput = (row) => `${this.statusColumn(row)} input`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;
  }

  /*
  Methods
   */

  /* Header Methods */
  /**
   * Go to new supplier page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewSupplierPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newSupplierLink);
  }

  /* Column Methods */
  /**
   * View Supplier
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async viewSupplier(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.viewRowLink(row));
  }

  /**
   * Edit Supplier
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async goToEditSupplierPage(page: Page, row: number = 1): Promise<void> {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Delete row in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @return {Promise<string>}
   */
  async deleteSupplier(page: Page, row: number = 1): Promise<string> {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(page, `${this.dropdownToggleButton(row)}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSuppliers(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get toggle column value for a row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number = 1): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(
      page,
      `${this.statusColumnToggleInput(row)}:checked`,
      'value',
    );

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Update Enable column for the value wanted in Brands list
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we want to enable status, false if not
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page: Page, row: number = 1, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForLoadState(page, this.statusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<string>}
   */
  async getTextColumnFromTableSupplier(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /* filter Methods */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value{ string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string = ''): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /**
   * Filter Supplier column active
   * @param page {Page} Browser tab
   * @param value {boolean} True if we need to filter by supplier enabled, false if not
   * @return {Promise<void>}
   */
  async filterSupplierEnabled(page: Page, value: boolean) : Promise<void> {
    await this.filterTable(page, 'select', 'active', value ? 'Yes' : 'No');
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to bulk enable status, false if not
   * @return {Promise<string>}
   */
  async bulkSetStatus(page: Page, enable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await page.click(enable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete with bulk actions
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async deleteWithBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.$eval(this.selectAllRowsLabel, (el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.bulkActionsDeleteButton),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteSuppliers(page);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete with modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteSuppliers(page: Page): Promise<void> {
    await this.clickAndWaitForLoadState(page, this.confirmDeleteButton);
  }

  // Sort methods
  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column name to get text content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i: number = 1; i <= rowsNumber; i++) {
      let rowContent = await this.getTextContent(page, this.tableColumn(i, column));

      if (column === 'active') {
        rowContent = (await this.getStatus(page, i)).toString();
      }
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
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
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
    await this.selectByVisibleText(page, this.paginationLimitSelect, number);

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

export default new Suppliers();
