import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * View feature page, contains functions that can be used on view feature page
 * @class
 * @extends BOBasePage
 */
class ViewFeature extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewValueLink: string;

  private readonly backToListButton: string;

  private readonly gridForm: string;

  private readonly gridPanel: string;

  private readonly gridHeaderTitle: string;

  private readonly gridTable: string;

  private readonly filterRow: string;

  private readonly filterSelectAll: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableBodyRows: string;

  private readonly tableBodyRow: (row: number) => string;

  private readonly tableBodyColumn: (row: number) => string;

  private readonly tableColumnId: (row: number) => string;

  private readonly tableColumnValue: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly paginationDiv: string;

  private readonly paginationSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on feature page
   */
  constructor() {
    super();

    this.pageTitle = 'Features';
    this.successfulMultiDeleteMessage = 'Successful deletion';

    // Header selectors
    this.addNewValueLink = 'a#page-header-desc-configuration-add_feature_value[title="Add new feature value"]';

    // Form selectors
    this.gridForm = '#form-feature_value';
    this.gridPanel = '#feature_value_grid_panel';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Table selectors
    this.gridTable = '#feature_value_grid_table';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterSelectAll = `${this.filterRow} .grid_bulk_action_select_all`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='feature_value[${filterBy}]']`;
    this.filterSearchButton = `${this.filterRow} button[name="feature_value[actions][search]"]`;
    this.filterResetButton = `${this.filterRow} button[name="feature_value[actions][reset]"]`;

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnValue = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.grid-edit-row-link`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} a.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.grid-delete-row-link`;

    // Bulk actions selectors
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsDeleteButton = '#feature_value_grid_bulk_action_delete_selection';
    this.confirmDeleteModal = '#feature_value-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Pagination selectors
    this.paginationDiv = `${this.gridPanel} .pagination-block`;
    this.paginationSelect = `${this.paginationDiv} #paginator_select_page_limit`;
    this.paginationLabel = `${this.paginationDiv} .col-form-label`;
    this.paginationPreviousLink = `${this.paginationDiv} a[data-role="previous-page-link"]`;
    this.paginationNextLink = `${this.paginationDiv} a[data-role="next-page-link"]`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Footer selectors
    this.backToListButton = `${this.gridPanel} .card-footer a`;
  }

  /* Header methods */

  /**
   * Go to add new value page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewValuePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewValueLink);
  }

  /**
   * Go to edit value page
   * @param page {Page} Browser tab
   * @param row {number} Row in values table
   * @return {Promise<void>}
   */
  async goToEditValuePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Click on back to the list button
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnBackToTheListButton(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.backToListButton);
  }

  /**
   * Delete value
   * @param page {Page} Browser tab
   * @param row {number} Row in values table
   * @return {Promise<string>}
   */
  async deleteValue(page: Page, row: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.tableColumnActionsToggleButton(row));
    await this.waitForSelectorAndClick(page, this.tableColumnActionsDeleteLink(row));
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Filter methods */
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
   * Get Number of feature values
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset and get number of feature values
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /**
   * Filter table
   * @param page {Page} Browser tab
   * @param filterBy {string} Column to filter with
   * @param value {string} value to filter with
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterBy: string, value: string): Promise<void> {
    await this.setValue(page, this.filterColumn(filterBy), value);
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /* Column methods */
  /**
   * Get text column from table
   * @param page {Page} Browser tab
   * @param row {number} Feature value row on table
   * @param columnName Column name of the value to return
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'id_feature_value':
        columnSelector = this.tableColumnId(row);
        break;

      case 'value':
        columnSelector = this.tableColumnValue(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /* Bulk actions methods */
  /**
   * Bulk delete attributes
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteValues(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.filterSelectAll).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);

    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, this.bulkActionsToggleButton),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await page.locator(this.confirmDeleteButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getPaginationLabel(page: Page): Promise<number> {
    const found = await this.elementVisible(page, this.paginationNextLink, 1000);

    // In case we filter products and there is only one page, link next from pagination does not appear
    if (!found) {
      return page.locator(this.tableBodyRows).count();
    }

    const footerText = await this.getTextContent(page, this.paginationLabel);
    const regexMatch: RegExpMatchArray|null = footerText.match(/page ([0-9]+)/);

    if (regexMatch === null) {
      return 0;
    }
    const regexResult: RegExpExecArray|null = /\d+/g.exec(regexMatch.toString());

    if (regexResult === null) {
      return 0;
    }

    return parseInt(regexResult.toString(), 10);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination number to select
   * @returns {Promise<number>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<number> {
    await this.selectByVisibleText(page, this.paginationSelect, number);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async paginationNext(page: Page): Promise<number> {
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async paginationPrevious(page: Page): Promise<number> {
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i: number = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.locator(sortColumnSpanButton).click();
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param columnName Column name on table
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
}

export default new ViewFeature();
