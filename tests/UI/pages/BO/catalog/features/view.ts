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

  private readonly tableColumnValue: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly deleteModalButtonYes: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on feature page
   */
  constructor() {
    super();

    this.pageTitle = 'Features >';

    // Header selectors
    this.addNewValueLink = '#page-header-desc-feature_value-new_feature_value';

    // Form selectors
    this.gridForm = '#form-feature_value';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#table-feature_value';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.filter`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='feature_valueFilter_${filterBy}']`;
    this.filterSearchButton = '#submitFilterButtonfeature_value';
    this.filterResetButton = 'button[name=\'submitResetfeature_value\']';

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
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} a.edit`;
    this.tableColumnActionsToggleButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row: number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActionsDropdownMenu(row)} a.delete`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_feature_value';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

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

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';

    // Footer selectors
    this.backToListButton = '#desc-feature_value-back';
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
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    return this.getAlertSuccessBlockContent(page);
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
  getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTableNumberOfTitlesSpan);
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
   * @param number {number} Pagination number to select
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

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector: string;

    switch (sortBy) {
      case 'id_feature_value':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'value':
        columnSelector = this.sortColumnDiv(3);
        break;

      default:
        throw new Error(`Column ${sortBy} was not found`);
    }

    const sortColumnButton = `${columnSelector} i.icon-caret-${sortDirection}`;
    await this.clickAndWaitForURL(page, sortColumnButton);
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
