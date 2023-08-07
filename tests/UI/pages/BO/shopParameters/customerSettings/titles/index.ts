import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Titles page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Titles extends BOBasePage {
  public readonly pageTitle: string;

  private readonly newTitleLink: string;

  private readonly gridForm: string;

  private readonly gridTableHeaderTitle: string;

  private readonly gridTableNumberOfTitlesSpan: string;

  private readonly gridTable: string;

  private readonly gridPanel: string;

  private readonly gridTitle: string;

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

  private readonly tableColumnTitle: (row: number) => string;

  private readonly tableColumnGender: (row: number) => string;

  private readonly tableColumnActions: (row: number) => string;

  private readonly tableColumnActionsEditLink: (row: number) => string;

  private readonly tableColumnActionsToggleButton: (row: number) => string;

  private readonly tableColumnActionsDropdownMenu: (row: number) => string;

  private readonly tableColumnActionsDeleteLink: (row: number) => string;

  private readonly deleteModalButtonYes: string;

  private readonly bulkActionsToggleButton: string;

  private readonly deleteSelectionButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on titles page
   */
  constructor() {
    super();

    this.pageTitle = 'Titles •';
    this.successfulUpdateMessage = 'Update successful';
    this.successfulMultiDeleteMessage = 'Successful deletion';

    // Header selectors
    this.newTitleLink = '#page-header-desc-configuration-add[title=\'Add new title\']';

    // Form selectors
    this.gridForm = '#form-gender';
    this.gridTableHeaderTitle = `${this.gridForm} .panel-heading`;
    this.gridTableNumberOfTitlesSpan = `${this.gridTableHeaderTitle} span.badge`;

    // Table selectors
    this.gridTable = '#title_grid';
    this.gridPanel = '#title_grid_panel';
    this.gridTitle = `${this.gridPanel} h3.card-header-title`;

    // Filter selectors
    this.filterRow = `${this.gridTable} tr.column-filters`;
    this.filterSelectAll = `${this.filterRow} td[data-column-id="title_bulk"] .md-checkbox label`;
    this.filterColumn = (filterBy: string) => `${this.filterRow} [name='title[${filterBy}]']`;
    this.filterSearchButton = `${this.filterRow} button[name='title[actions][search]']`;
    this.filterResetButton = `${this.filterRow} button[name='title[actions][reset]']`;

    // Table body selectors
    this.tableBody = `${this.gridTable} tbody`;
    this.tableBodyRows = `${this.tableBody} tr`;
    this.tableBodyRow = (row:number) => `${this.tableBodyRows}:nth-child(${row})`;
    this.tableBodyColumn = (row:number) => `${this.tableBodyRow(row)} td`;

    // Columns selectors
    this.tableColumnId = (row:number) => `${this.tableBodyColumn(row)}:nth-child(2)`;
    this.tableColumnTitle = (row:number) => `${this.tableBodyColumn(row)}:nth-child(3)`;
    this.tableColumnGender = (row:number) => `${this.tableBodyColumn(row)}:nth-child(4)`;

    // Row actions selectors
    this.tableColumnActions = (row:number) => `${this.tableBodyColumn(row)} .btn-group-action`;
    this.tableColumnActionsEditLink = (row:number) => `${this.tableColumnActions(row)} a.grid-edit-row-link`;
    this.tableColumnActionsToggleButton = (row:number) => `${this.tableColumnActions(row)} a.dropdown-toggle`;
    this.tableColumnActionsDropdownMenu = (row:number) => `${this.tableColumnActions(row)} .dropdown-menu`;
    this.tableColumnActionsDeleteLink = (row:number) => `${this.tableColumnActionsDropdownMenu(row)} a.grid-delete-row-link`;

    // Confirmation modal
    this.deleteModalButtonYes = '#title-grid-confirm-modal button.btn-confirm-submit';

    // Bulk actions selectors
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.deleteSelectionButton = `${this.gridPanel} #title_grid_bulk_action_delete_selection`;
  }

  /* Header methods */

  /**
   * Go to add new title page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewTitle(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newTitleLink);
  }

  /* Filter methods */

  /**
   * Filter titles
   * @param page {Page} Browser tab
   * @param filterType {string} Type of filter field( input/select)
   * @param filterBy {string} Column to filter with
   * @param value {string} Value to filter
   * @return {Promise<void>}
   */
  async filterTitles(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    const currentUrl: string = page.url();

    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value);
        await this.clickAndWaitForURL(page, this.filterSearchButton);
        break;

      default:
        throw new Error(`Filter ${filterBy} was not found`);
    }
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
   * Get number of titles
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridTitle);
  }

  /**
   * Reset and get number of titles
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
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
    let columnSelector: string;

    switch (columnName) {
      case 'id_gender':
        columnSelector = this.tableColumnId(row);
        break;

      case 'name':
        columnSelector = this.tableColumnTitle(row);
        break;

      case 'type':
        columnSelector = this.tableColumnGender(row);
        break;

      default:
        throw new Error(`Column ${columnName} was not found`);
    }

    return this.getTextContent(page, columnSelector);
  }

  /**
   * Go to edit title page
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<void>}
   */
  async gotoEditTitlePage(page: Page, row: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.tableColumnActionsEditLink(row));
  }

  /**
   * Delete title from row
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<string>}
   */
  async deleteTitle(page: Page, row: number): Promise<string> {
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
   * Bulk delete titles
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async bulkDeleteTitles(page: Page): Promise<string> {
    // Select all rows
    await page.$eval(this.filterSelectAll, (el: HTMLElement) => el.click());
    await this.waitForVisibleSelector(page, this.bulkActionsToggleButton);

    // Click on Button Bulk actions
    await Promise.all([
      page.click(this.bulkActionsToggleButton),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteSelectionButton),
      this.waitForVisibleSelector(page, this.deleteModalButtonYes),
    ]);
    await this.clickAndWaitForURL(page, this.deleteModalButtonYes);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Titles();
