require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

/**
 * Currencies page, contains functions that can be used on the page
 * @class
 * @extends LocalizationBasePage
 */
class Currencies extends LocalizationBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on currencies page
   */
  constructor() {
    super();

    this.pageTitle = 'Currencies • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header Selectors
    this.newCurrencyLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#currency_grid_panel';
    this.gridTable = '#currency_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Filters
    this.filterColumn = filterBy => `${this.gridTable} #currency_${filterBy}`;
    this.filterSearchButton = `${this.gridTable} .grid-search-button`;
    this.filterResetButton = `${this.gridTable} .grid-reset-button`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = row => `${this.tableBody} tr:nth-child(${row})`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = (row, column) => `${this.tableRow(row)} td.column-${column}`;
    // enable column
    this.statusColumn = row => `${this.tableColumn(row, 'active')} .ps-switch`;
    this.statusColumnToggleInput = row => `${this.statusColumn(row)} input`;

    // Actions buttons in row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    this.editRowLink = row => `${this.actionsColumn(row)} a[href*='/edit']`;

    // Delete modal
    this.confirmDeleteModal = '#currency-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;

    // Exchange rate form
    this.updateExchangeRatesButton = '#update-exchange-rates-button';

    // Pagination selectors
    this.paginationLimitSelect = '#paginator_select_page_limit';
    this.paginationLabel = `${this.gridPanel} .col-form-label`;
    this.paginationNextLink = `${this.gridPanel} #pagination_next_url`;
    this.paginationPreviousLink = `${this.gridPanel} [aria-label='Previous']`;

    // Sort Selectors
    this.tableHead = `${this.gridTable} thead`;
    this.sortColumnDiv = column => `${this.tableHead} div.ps-sortable-column[data-sort-col-name='${column}']`;
    this.sortColumnSpanButton = column => `${this.sortColumnDiv(column)} span.ps-sort`;
  }

  /* Header Methods */
  /**
   * Go to add new currency page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAddNewCurrencyPage(page) {
    await this.clickAndWaitForNavigation(page, this.newCurrencyLink);
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
  async filterTable(page, filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(page, this.filterColumn(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
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
  async getTextColumnFromTableCurrency(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Get exchange rate value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<number>}
   */
  async getExchangeRateValue(page, row) {
    return this.getNumberFromText(page, this.tableColumn(row, 'conversion_rate'));
  }

  /**
   * Get currency row from table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @returns {Promise<{symbol: string, isoCode: string, exchangeRate: number, name: string, enabled: boolean}>}
   */
  async getCurrencyFromTable(page, row) {
    return {
      name: await this.getTextColumnFromTableCurrency(page, row, 'name'),
      symbol: await this.getTextColumnFromTableCurrency(page, row, 'symbol'),
      isoCode: await this.getTextColumnFromTableCurrency(page, row, 'iso_code'),
      exchangeRate: await this.getExchangeRateValue(page, row),
      enabled: await this.getStatus(page, row),
    };
  }

  /**
   * Get a currency status
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<boolean>}
   */
  async getStatus(page, row = 1) {
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
  async setStatus(page, row = 1, valueWanted = true) {
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.statusColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Delete Row in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table to delete
   * @returns {Promise<string>}
   */
  async deleteCurrency(page, row = 1) {
    await Promise.all([
      page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        page,
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(page, `${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteCurrency(page);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Confirm delete in modal
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async confirmDeleteCurrency(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Go to edit currency page
   * @param page {Page} Browser tab
   * @param row {number} Row on table to edit
   * @returns {Promise<void>}
   */
  async goToEditCurrencyPage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(row));
  }

  /**
   * Click on update exchange rates
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async updateExchangeRate(page) {
    await this.clickAndWaitForNavigation(page, this.updateExchangeRatesButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Pagination methods */
  /**
   * Get pagination label
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getPaginationLabel(page) {
    return this.getTextContent(page, this.paginationLabel);
  }

  /**
   * Select pagination limit
   * @param page {Page} Browser tab
   * @param number {number} Pagination number to select
   * @returns {Promise<string>}
   */
  async selectPaginationLimit(page, number) {
    await Promise.all([
      this.selectByVisibleText(page, this.paginationLimitSelect, number),
      page.waitForNavigation({waitUntil: 'networkidle'}),
    ]);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on next
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationNext(page) {
    await this.clickAndWaitForNavigation(page, this.paginationNextLink);

    return this.getPaginationLabel(page);
  }

  /**
   * Click on previous
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async paginationPrevious(page) {
    await this.clickAndWaitForNavigation(page, this.paginationPreviousLink);

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
  async sortTable(page, sortBy, sortDirection) {
    const sortColumnDiv = `${this.sortColumnDiv(sortBy)}[data-sort-direction='${sortDirection}']`;
    const sortColumnSpanButton = this.sortColumnSpanButton(sortBy);

    let i = 0;
    while (await this.elementNotVisible(page, sortColumnDiv, 2000) && i < 2) {
      await page.hover(this.sortColumnDiv(sortBy));
      await this.clickAndWaitForNavigation(page, sortColumnSpanButton);
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
  async getAllRowsColumnContent(page, column) {
    const rowsNumber = await this.getNumberOfElementInGrid(page);
    const allRowsContentTable = [];

    for (let i = 1; i <= rowsNumber; i++) {
      const rowContent = await this.getTextColumnFromTableCurrency(page, i, column);
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }
}

module.exports = new Currencies();
