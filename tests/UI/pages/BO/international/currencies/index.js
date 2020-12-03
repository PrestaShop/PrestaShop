require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

class Currencies extends LocalizationBasePage {
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
    this.enableColumn = row => this.tableColumn(row, 'active');
    this.enableColumnValidIcon = row => `${this.enableColumn(row)} i.grid-toggler-icon-valid`;
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
  }

  /* Header Methods */
  /**
   * Go to add new currency page
   * @param page
   * @return {Promise<void>}
   */
  async goToAddNewCurrencyPage(page) {
    await this.clickAndWaitForNavigation(page, this.newCurrencyLink);
  }

  /* filter Method */
  /**
   * Filter Table
   * @param page
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
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
   * @param page
   * @return {Promise<void>}
   */
  async resetFilter(page) {
    if (await this.elementVisible(page, this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @param page
   * @returns {Promise<number>}
   */
  async resetAndGetNumberOfLines(page) {
    await this.resetFilter(page);
    return this.getNumberOfElementInGrid(page);
  }

  /* Table methods */
  /**
   * get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCurrency(page, row, column) {
    return this.getTextContent(page, this.tableColumn(row, column));
  }

  /**
   * Get exchange rate value
   * @param page
   * @param row
   * @returns {Promise<number>}
   */
  async getExchangeRateValue(page, row) {
    return this.getNumberFromText(page, this.tableColumn(row, 'conversion_rate'));
  }

  /**
   * Get currency row from table
   * @param page
   * @param row
   * @returns {Promise<{symbol: string, isoCode: string, exchangeRate: number, name: string, enabled: string}>}
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
   * Get toggle column value for a row
   * @param page
   * @param row
   * @return {Promise<boolean>}
   */
  async getStatus(page, row = 1) {
    return this.elementVisible(page, this.enableColumnValidIcon(row), 100);
  }

  /**
   * Update Enable column for the value wanted in currency list
   * @param page
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async setStatus(page, row = 1, valueWanted = true) {
    await this.waitForVisibleSelector(page, this.enableColumn(row), 2000);
    if (await this.getStatus(page, row) !== valueWanted) {
      await this.clickAndWaitForNavigation(page, this.enableColumn(row));
      return true;
    }

    return false;
  }

  /**
   * Delete Row in table
   * @param page
   * @param row, row to delete
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
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete in modal
   * @param page
   * @return {Promise<void>}
   */
  async confirmDeleteCurrency(page) {
    await this.clickAndWaitForNavigation(page, this.confirmDeleteButton);
  }

  /**
   * Go to edit currency page
   * @param page
   * @param row
   * @returns {Promise<void>}
   */
  async goToEditCurrencyPage(page, row = 1) {
    await this.clickAndWaitForNavigation(page, this.editRowLink(row));
  }

  /**
   * Click on update exchange rates
   * @param page
   * @returns {Promise<string>}
   */
  async updateExchangeRate(page) {
    await this.clickAndWaitForNavigation(page, this.updateExchangeRatesButton);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new Currencies();
