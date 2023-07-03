import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Features page, contains functions that can be used on features page
 * @class
 * @extends BOBasePage
 */
class Features extends BOBasePage {
  public readonly pageTitle: string;

  private readonly addNewFeatureLink: string;

  private readonly helpCardLink: string;

  private readonly helpContainerBlock: string;

  private readonly gridForm: string;

  private readonly gridPanel: string;

  private readonly gridHeaderTitle: string;

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

  private readonly tableColumnValues: (row: number) => string;

  private readonly tableColumnPosition: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsViewLink: (row: number) => string;

  private readonly tableColumnActionsDropDownButton: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly paginationActiveLabel: string;

  private readonly paginationDiv: string;

  private readonly paginationDropdownButton: string;

  private readonly paginationItems: (number: number) => string;

  private readonly paginationPreviousLink: string;

  private readonly paginationNextLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: number) => string;

  private readonly sortColumnSpanButton: (column: number) => string;

  private readonly bulkActionBlock: string;

  private readonly bulkActionMenuButton: string;

  private readonly bulkActionDropdownMenu: string;

  private readonly selectAllLink: string;

  private readonly bulkDeleteLink: string;

  private readonly deleteModalButtonYes: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on features page
   */
  constructor() {
    super();

    this.pageTitle = 'Features • ';

    // Header selectors
    this.addNewFeatureLink = 'a#page-header-desc-configuration-add_feature[title="Add new feature"]';

    // Help card selectors
    this.helpCardLink = '#toolbar-nav a.btn-help';
    this.helpContainerBlock = '#help-container';

    // Form selectors
    this.gridForm = '#feature_value_filter_form';
    this.gridPanel = '#feature_grid_panel';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Table selectors
    this.gridTable = '#feature_grid_table';

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='feature[${filterBy}]']`;
    this.filterSearchButton = `${this.filterRow} button[name="feature[actions][search]"]`;
    this.filterResetButton = `${this.filterRow} button[name="feature[actions][reset]"]`;

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row: number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row: number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row: number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnName = (row: number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnValues = (row: number) => `${this.tableBodyColumn(row)}:nth-child(4)`;
    this.tableColumnPosition = (row: number) => `${this.tableBodyColumn(row)}:nth-child(5)`;

    // Row actions selectors
    this.tableColumnActions = (row: number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsViewLink = (row: number) => `${this.tableColumnActions(row)} a.grid-view-row-link`;
    this.tableColumnActionsDropDownButton = (row: number) => `${this.tableColumnActions(row)} button.dropdown-toggle`;
    this.tableColumnActionsEditLink = (row: number) => `${this.tableColumnActions(row)} li a.edit`;
    this.tableColumnActionsDeleteLink = (row: number) => `${this.tableColumnActions(row)} li a.delete`;

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
    this.sortColumnSpanButton = (column: number) => `${this.sortColumnDiv(column)} span.ps-sort`;

    // Bulk actions selectors
    this.bulkActionBlock = 'div.bulk-actions';
    this.bulkActionMenuButton = '#bulk_action_menu_feature';
    this.bulkActionDropdownMenu = `${this.bulkActionBlock} ul.dropdown-menu`;
    this.selectAllLink = `${this.bulkActionDropdownMenu} li:nth-child(1)`;
    this.bulkDeleteLink = `${this.bulkActionDropdownMenu} li:nth-child(4)`;

    // Growl message
    this.growlMessageBlock = '#growls .growl-message';

    // Confirmation modal
    this.deleteModalButtonYes = '#popup_ok';
  }

  /* Header methods */
  /**
   * Go to add new feature page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddFeaturePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewFeatureLink);
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
   * Get Number of features
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset and get number of features
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
   * @param row {number} Feature row in table
   * @param columnName {string} Column name to get
   * @return {Promise<string>}
   */
  async getTextColumn(page: Page, row: number, columnName: string): Promise<string> {
    let columnSelector: string;

    switch (columnName) {
      case 'id_feature':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnName(row);
        break;

      case 'values':
        columnSelector = this.tableColumnValues(row);
        break;

      case 'position':
        columnSelector = this.tableColumnPosition(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to view feature page
   * @param page {Page} Browser tab
   * @param row {number} Feature row in table
   * @return {Promise<void>}
   */
  async viewFeature(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsViewLink(row));
  }

  /**
   * Click on edit feature
   * @param page {Page} Browser tab
   * @param row {number} Feature row in table
   * @return {Promise<void>}
   */
  async clickOnEditFeature(page: Page, row: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.tableColumnActionsDropDownButton(row));
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete feature
   * @param page {Page} Browser tab
   * @param row {number} Feature row in table
   * @return {Promise<string>}
   */
  async deleteFeature(page: Page, row: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.tableColumnActionsDropDownButton(row));
    await this.clickAndWaitForURL(page, this.tableColumnActionsDeleteLink(row));

    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    return this.getAlertSuccessBlockContent(page);
  }

  /* Helper card methods */
  /**
   * @override
   * Open help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page: Page): Promise<boolean> {
    await page.click(this.helpCardLink);

    return this.elementVisible(page, this.helpContainerBlock, 4000);
  }

  /**
   * @override
   * Close help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page: Page): Promise<boolean> {
    await page.click(this.helpCardLink);

    return this.elementNotVisible(page, this.helpContainerBlock, 2000);
  }

  /**
   * @override
   * Get help card URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getHelpDocumentURL(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.helpCardLink, 'href');
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
   * @param number {number} Pagination limit number to select
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
   * @param sortBy {string} column name to sort with
   * @param sortDirection {string} Sort direction by asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    let columnSelector: string;

    switch (sortBy) {
      case 'id_feature':
        columnSelector = this.sortColumnDiv(2);
        break;

      case 'b!name':
        columnSelector = this.sortColumnDiv(3);
        break;

      case 'a!position':
        columnSelector = this.sortColumnDiv(5);
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
   * @param columnName {string} Column name to get all content
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

  /* Bulk actions methods */
  /**
   * Bulk delete features
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteFeatures(page: Page): Promise<string> {
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

    await Promise.all([
      page.click(this.bulkActionMenuButton),
      this.waitForVisibleSelector(page, this.bulkDeleteLink),
    ]);

    await this.clickAndWaitForURL(page, this.bulkDeleteLink);

    // Return successful message
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Change feature position
   * @param page {Page} Browser tab
   * @param actualPosition {number} Actual position to change
   * @param newPosition {number} New position to change
   * @return {Promise<string|null>}
   */
  async changePosition(page: Page, actualPosition: number, newPosition: number): Promise<string | null> {
    await this.dragAndDrop(
      page,
      this.tableColumnPosition(actualPosition),
      this.tableColumnPosition(newPosition),
    );

    return page.textContent(this.growlMessageBlock);
  }
}

export default new Features();
