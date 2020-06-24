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
    this.filterSearchButton = `${this.gridTable} button[name='currency[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='currency[actions][reset]']`;

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
    this.deleteRowLink = row => `${this.dropdownToggleMenu(row)} a[data-url*='/delete']`;
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
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @returns {Promise<number>}
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
   * @returns {Promise<string>}
   */
  async getTextColumnFromTableCurrency(row, column) {
    return this.getTextContent(this.tableColumn(row, column));
  }

  /**
   * Get exchange rate value
   * @param row
   * @returns {Promise<number>}
   */
  async getExchangeRateValue(row) {
    return this.getNumberFromText(this.tableColumn(row, 'conversion_rate'));
  }

  /**
   * Get currency row from table
   * @param row
   * @returns {Promise<{symbol: string, isoCode: string, exchangeRate: number, name: string, enabled: string}>}
   */
  async getCurrencyFromTable(row) {
    return {
      name: await this.getTextColumnFromTableCurrency(row, 'currency'),
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
   * @returns {Promise<string>}
   */
  async deleteCurrency(row = 1) {
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton(row)),
      this.waitForVisibleSelector(
        `${this.dropdownToggleButton(row)}[aria-expanded='true']`,
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink(row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
