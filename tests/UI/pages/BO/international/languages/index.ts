import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import type {Page} from 'playwright';

/**
 * Languages page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class Languages extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  public readonly unSuccessfulUpdateDefaultLanguageStatusMessage: string;

  private readonly addNewLanguageLink: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly statusColumn: (row: number) => string;

  private readonly statusColumnToggleInput: (row: number) => string;

  private readonly selectAllRowsLabel: string;

  private readonly bulkActionsToggleButton: string;

  private readonly bulkActionsEnableButton: string;

  private readonly bulkActionsDisableButton: string;

  private readonly bulkActionsDeleteButton: string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on languages page
   */
  constructor() {
    super();

    this.pageTitle = 'Languages •';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.unSuccessfulUpdateDefaultLanguageStatusMessage = 'You cannot change the status of the default language.';

    // Header selectors
    this.addNewLanguageLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#language_grid_panel';
    this.gridTable = '#language_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #language_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;

    // Column actions selectors
    this.actionsColumn = (row: number) => `${this.tableRow(row)} td.column-actions`;
    this.editRowLink = (row: number) => `${this.actionsColumn(row)} a.grid-edit-row-link`;
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    this.statusColumn = (row: number) => `${this.tableColumn(row, 'active')} .ps-switch`;
    this.statusColumnToggleInput = (row: number) => `${this.statusColumn(row)} input`;

    // Bulk Actions
    this.selectAllRowsLabel = `${this.gridPanel} tr.column-filters .md-checkbox i`;
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.bulkActionsEnableButton = '#language_grid_bulk_action_enable_selection';
    this.bulkActionsDisableButton = '#language_grid_bulk_action_disable_selection';
    this.bulkActionsDeleteButton = '#language_grid_bulk_action_delete_selection';
    this.confirmDeleteModal = '#language-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

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

  /* Header methods */
  /**
   * Go to add new language page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewLanguage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewLanguageLink);
  }

  /* Reset methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page: Page): Promise<void> {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForLoadState(page, this.filterResetButton);
      await this.elementNotVisible(page, this.filterResetButton, 2000);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset filter and get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /* Filter method */
  /**
   * Filter Table
   * @param page {Page} Browser tab
   * @param filterType {string} Input or select to choose method of filter
   * @param filterBy {string} Column to filter
   * @param value {string} Value to put on filter
   * @return {Promise<void>}
   */
  async filterTable(page: Page, filterType: string, filterBy: string, value: string): Promise<void> {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value === '1' ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForURL(page, this.filterSearchButton);
  }

  /* Table methods */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTable(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTable(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to edit language page
   * @param page {Page} Browser tab
   * @param row {number} Row to edit on table
   * @return {Promise<void>}
   */
  async goToEditLanguage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row to delete on table
   * @returns {Promise<string>}
   */
  async deleteLanguage(page: Page, row: number = 1): Promise<string> {
    await Promise.all([
      page.locator(this.dropdownToggleButton(row)).click(),
      this.waitForVisibleSelector(
        page,
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);

    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteRowLink(row)).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteLanguages(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get language status
   * @param page {Page} Browser tab
   * @param row {number} Row to get status
   * @return {Promise<boolean>}
   */
  async getStatus(page: Page, row: number): Promise<boolean> {
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
   * Enable/Disable language
   * @param page {Page} Browser tab
   * @param row {number} Row on table to set status
   * @param valueWanted {boolean} True if we need to enable status
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page: Page, row: number, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await page.locator(this.statusColumn(row)).click();

      return true;
    }

    return false;
  }

  /* Bulk Actions Methods */
  /**
   * Enable / disable Suppliers by Bulk Actions
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable status, false if not
   * @returns {Promise<string>}
   */
  async bulkSetStatus(page: Page, toEnable: boolean = true): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await page.locator(toEnable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton).click();
    await this.elementNotVisible(page, toEnable ? this.bulkActionsEnableButton : this.bulkActionsDisableButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete with bulk actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteWithBulkActions(page: Page): Promise<string> {
    // Click on Select All
    await Promise.all([
      page.locator(this.selectAllRowsLabel).evaluate((el: HTMLElement) => el.click()),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}:not([disabled])`),
    ]);
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.bulkActionsDeleteButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteLanguages(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteLanguages(page: Page): Promise<void> {
    await page.locator(this.confirmDeleteButton).click();
    await this.elementNotVisible(page, this.confirmDeleteButton, 2000);
  }

  /* Sort functions */
  /**
   * Sort table by clicking on column name
   * @param page {Page} Browser tab
   * @param sortBy {string} Column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string = 'asc'): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
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
   * @param number {number} Number of pagination limit to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page: Page, number: number): Promise<string> {
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page: Page): Promise<string> {
    await this.scrollTo(page, this.paginationNextLink);
    await this.clickAndWaitForURL(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page: Page): Promise<string> {
    await this.scrollTo(page, this.paginationPreviousLink);
    await this.clickAndWaitForURL(page, this.paginationPreviousLink);

    return this.getPaginationLabel(page);
  }
}

export default new Languages();
