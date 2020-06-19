require('module-alias/register');
const LocalizationBasePage = require('@pages/BO/international/localization/localizationBasePage');

module.exports = class Currencies extends LocalizationBasePage {
  constructor(page) {
    super(page);

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
    this.enableColumnNotValidIcon = row => `${this.enableColumn(row)} i.grid-toggler-icon-not-valid`;
    // Actions buttons in row
    this.actionsColumn = row => `${this.tableRow(row)} td.column-actions`;
    this.dropdownToggleButton = row => `${this.actionsColumn(row)} a.dropdown-toggle`;
    this.dropdownToggleMenu = row => `${this.actionsColumn(row)} div.dropdown-menu`;
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a.grid-delete-row-link`;
    // Delete modal
    this.confirmDeleteModal = '#currency-grid-confirm-modal';
    this.confirmDeleteButton = `${this.confirmDeleteModal} button.btn-confirm-submit`;
  }

  /* Header Methods */
  /**
   * Go to add new currency page
   * @return {Promise<void>}
   */
  async goToAddNewCurrencyPage() {
    await this.clickAndWaitForNavigation(this.newCurrencyLink);
  }

  /* filter Method */
  /**
   * Filter Table
   * @param filterType, input / Select
   * @param filterBy, which column
   * @param value, value to put in filter
   * @return {Promise<void>}
   */
  async filterTable(filterType, filterBy, value) {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn(filterBy), value ? 'Yes' : 'No');
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /* Reset Methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (await this.elementVisible(this.filterResetButton, 2000)) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /* Table methods */
  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableCurrency(row, column) {
    return this.getTextContent(this.tableColumn(row, column));
  }

  /**
   * Get exchange rate value
   * @param row
   * @return {Promise<integer>}
   */
  async getExchangeRateValue(row) {
    return this.getNumberFromText(this.tableColumn(row, 'conversion_rate'));
  }

  /**
   * Get currency row from table
   * @param row
   * @return {Promise<object>}
   */
  async getCurrencyFromTable(row) {
    return {
      name: await this.getTextColumnFromTableCurrency(row, 'name'),
      symbol: await this.getTextColumnFromTableCurrency(row, 'symbol'),
      isoCode: await this.getTextColumnFromTableCurrency(row, 'iso_code'),
      exchangeRate: await this.getExchangeRateValue(row),
      enabled: await this.getToggleColumnValue(row),
    };
  }

  /**
   * Get toggle column value for a row
   * @param row
   * @return {Promise<string>}
   */
  async getToggleColumnValue(row = 1) {
    return this.elementVisible(this.enableColumnValidIcon(row), 100);
  }

  /**
   * Update Enable column for the value wanted in currency list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row = 1, valueWanted = true) {
    await this.waitForVisibleSelector(this.enableColumn(row), 2000);
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.enableColumn(row));
      return true;
    }
    return false;
  }

  /**
   * Delete Row in table
   * @param row, row to delete
   * @return {Promise<textContent>}
   */
  async deleteCurrency(row = 1) {
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    // Click on delete and wait for modal
    await Promise.all([
      this.page.click(this.deleteRowLink(row)),
      this.waitForVisibleSelector(`${this.confirmDeleteModal}.show`),
    ]);
    await this.confirmDeleteCurrency();
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Confirm delete in modal
   * @return {Promise<void>}
   */
  async confirmDeleteCurrency() {
    await this.clickAndWaitForNavigation(this.confirmDeleteButton);
  }
};
