import LocalizationBasePage from '@pages/BO/international/localization/localizationBasePage';

import CurrencyData from '@data/faker/currency';

import type {Page} from 'playwright';

/**
 * Currencies page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class Currencies extends LocalizationBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateStatusMessage: string;

  public readonly cannotDisableDefaultCurrencyMessage: string;

  public readonly cannotDeleteDefaultCurrencyMessage: string;

  private readonly newCurrencyLink: string;

  private readonly gridPanel: string;

  private readonly gridTable: string;

  private readonly gridHeaderTitle: string;

  private readonly bulkActionsToggleButton: string;

  private readonly enableSelectionButton: string;

  private readonly disableSelectionButton: string;

  private readonly deleteSelectionButton: string;

  private readonly filterColumn: (filterBy: string) => string;

  private readonly filterSearchButton: string;

  private readonly filterResetButton: string;

  private readonly tableBody: string;

  private readonly tableRow: (row: number) => string;

  private readonly tableEmptyRow: string;

  private readonly tableColumn: (row: number, column: string) => string;

  private readonly bulkSelectColumn: (row: number) => string;

  private readonly bulkSelectColumnCheckbox: (row: number) => string;

  private readonly statusColumn: (row: number) => string;

  private readonly statusColumnToggleInput: (row: number) => string;

  private readonly actionsColumn: (row: number) => string;

  private readonly dropdownToggleButton: (row: number) => string;

  private readonly dropdownToggleMenu: (row: number) => string;

  private readonly deleteRowLink: (row: number) => string;

  private readonly editRowLink: (row: number) => string;

  private readonly confirmDeleteModal: string;

  private readonly confirmDeleteButton: string;

  private readonly updateExchangeRatesButton: string;

  private readonly paginationLimitSelect: string;

  private readonly paginationLabel: string;

  private readonly paginationNextLink: string;

  private readonly paginationPreviousLink: string;

  private readonly tableHead: string;

  private readonly sortColumnDiv: (column: string) => string;

  private readonly sortColumnSpanButton: (column: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on currencies page
   */
  constructor() {
    super();

    this.pageTitle = 'Currencies • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';
    this.cannotDisableDefaultCurrencyMessage = 'You cannot disable the default currency';
    this.cannotDeleteDefaultCurrencyMessage = 'You cannot delete the default currency';

    // Header Selectors
    this.newCurrencyLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#currency_grid_panel';
    this.gridTable = '#currency_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Bulk
    this.bulkActionsToggleButton = `${this.gridPanel} button.js-bulk-actions-btn`;
    this.enableSelectionButton = `${this.gridPanel} #currency_grid_bulk_action_enable_selection`;
    this.disableSelectionButton = `${this.gridPanel} #currency_grid_bulk_action_disable_selection`;
    this.deleteSelectionButton = `${this.gridPanel} #currency_grid_bulk_action_delete_selection`;

    // Filters
    this.filterColumn = (filterBy: string) => `${this.gridTable} #currency_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = (row: number) => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row: number, column: string) => `${this.tableRow(row)} td.column-${column}`;
    // Column Bulk Select
    this.bulkSelectColumn = (row: number) => this.tableColumn(row, 'currency_bulk');
    this.bulkSelectColumnCheckbox = (row: number) => `${this.bulkSelectColumn(row)} .md-checkbox label`;
    // Column Enabled
    this.statusColumn = (row: number) => `${this.tableColumn(row, 'active')} .ps-switch`;
    this.statusColumnToggleInput = (row: number) => `${this.statusColumn(row)} input`;
    // Columns Actions
    this.actionsColumn = (row: number) => this.tableColumn(row, 'actions');
    this.dropdownToggleButton = (row: number) => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = (row: number) => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = (row: number) => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    this.editRowLink = (row: number) => `${this.actionsColumn(row)} a[href*='/edit']`;

    // Delete modal
    this.confirmDeleteModal = '#currency-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Exchange rate form
    this.updateExchangeRatesButton = '#update-exchange-rates-button';

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} [data-role=next-page-link]`;
    this.paginationPreviousLink = `${this.gridPanel} [data-role='previous-page-link']`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = (column: string) => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = (column: string) => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header Methods */
  /**
   * Go to add new currency page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewCurrencyPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newCurrencyLink);
  }

  /* filter Method */
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
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value === '1' ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await page.locator(this.filterSearchButton).click();
    await this.elementVisible(page, this.filterResetButton);
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
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page: Page): Promise<number> {
    await this.resetFilter(page);

    return this.getNumberOfElementInGrid(page);
  }

  /* Table methods */
  /**
   * Get text from a column
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCurrency(page: Page, row: number, column: string): Promise<string> {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Get text for empty table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTextForEmptyTable(page: Page): Promise<string> {
    return this.getTextContent(page, this.tableEmptyRow);
  }

  /**
   * Get exchange rate value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<number>}
   */
  async getExchangeRateValue(page: Page, row: number): Promise<number> {
    return this.getPriceFromText(page, this.tableColumn(row, 'conversion_rate'));
  }

  /**
   * Get currency row from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<CurrencyData>}
   */
  async getCurrencyFromTable(page: Page, row: number): Promise<CurrencyData> {
    return new CurrencyData({
      name: await this.getTextColumnFromTableCurrency(page, row, 'name'),
      symbol: await this.getTextColumnFromTableCurrency(page, row, 'symbol'),
      isoCode: await this.getTextColumnFromTableCurrency(page, row, 'iso_code'),
      exchangeRate: await this.getExchangeRateValue(page, row),
      enabled: await this.getStatus(page, row),
    });
  }

  /**
   * Get a currency status
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
   * Set a currency status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param valueWanted {boolean} True if we need to enable status
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page: Page, row: number = 1, valueWanted: boolean = true): Promise<boolean> {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForLoadState(page, this.statusColumn(row));
      await this.elementVisible(page, this.alertTextBlock);
      return true;
    }

    return false;
  }

  /**
   * Select Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<void>}
   */
  async selectRow(page: Page, row: number): Promise<void> {
    await page.locator(this.bulkSelectColumnCheckbox(row)).evaluate((el: HTMLElement) => el.click());
  }

  /**
   * Returns if the button for "Bulk Actions" is enabled
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async isBulkActionsEnabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.bulkActionsToggleButton}:not([disabled])`, 1000);
  }

  /**
   * Delete all Taxes with Bulk Actions
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async bulkDeleteCurrencies(page: Page): Promise<string> {
    // Click on Button Bulk actions
    await Promise.all([
      page.locator(this.bulkActionsToggleButton).click(),
      this.waitForVisibleSelector(page, `${this.bulkActionsToggleButton}[aria-expanded='true']`),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.locator(this.deleteSelectionButton).click(),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.clickAndWaitForLoadState(page, this.confirmDeleteButton);
    await this.elementNotVisible(page, this.confirmDeleteModal);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteCurrency(page: Page, row: number = 1): Promise<string> {
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
    await this.confirmDeleteCurrency(page);

    if (await this.elementVisible(page, this.alertSuccessBlockParagraph, 2000)) {
      return this.getAlertSuccessBlockParagraphContent(page);
    }
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteCurrency(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.confirmDeleteButton);
  }

  /**
   * Go to edit currency page
   * @param page {Page} Browser tab
   * @param row {number} Row on table to edit
   * @returns {Promise<void>}
   */
  async goToEditCurrencyPage(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.editRowLink(row));
  }

  /**
   * Click on update exchange rates
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async updateExchangeRate(page: Page): Promise<string> {
    await page.locator(this.updateExchangeRatesButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
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
   * @param number {number} Pagination number to select
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
   * @param sortBy {string} column to sort with
   * @param sortDirection {string} Sort direction asc or desc
   * @return {Promise<void>}
   */
  async sortTable(page: Page, sortBy: string, sortDirection: string): Promise<void> {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForURL(page, sortColumnSpanButton);
      i += 1;
    }

    await this.waitForVisibleSelector(page, sortColumnDiv, 20000);
  }

  /**
   * Get content from all rows
   * @param page {Page} Browser tab
   * @param column {string} Column to get all rows content
   * @return {Promise<Array<string>>}
   */
  async getAllRowsColumnContent(page: Page, column: string): Promise<string[]> {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCurrency(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }
}

export default new Currencies();
