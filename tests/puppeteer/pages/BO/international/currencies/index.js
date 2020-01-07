require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Currencies extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Currencies • ';
    this.successfulUpdateStatusMessage = 'The status has been successfully updated.';

    // Header Selectors
    this.languagesNavItemLink = '#subtab-AdminLanguages';
    this.localizationNavItemLink = '#subtab-AdminLocalization';
    this.geolocationNavItemLink = '#subtab-AdminGeolocation';
    this.newCurrencyLink = '#page-header-desc-configuration-add';

    // Selectors grid panel
    this.gridPanel = '#currency_grid_panel';
    this.gridTable = '#currency_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;

    // Filters
    this.filterColumn = `${this.gridTable} #currency_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='currency[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='currency[actions][reset]']`;

    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // enable column
    this.enableColumn = this.tableColumn.replace('%COLUMN', 'active');
    this.enableColumnValidIcon = `${this.enableColumn} i.grid-toggler-icon-valid`;
    this.enableColumnNotValidIcon = `${this.enableColumn} i.grid-toggler-icon-not-valid`;
    // Actions buttons in row
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.dropdownToggleButton = `${this.actionsColumn} a.dropdown-toggle`;
    this.dropdownToggleMenu = `${this.actionsColumn} div.dropdown-menu`;
    this.deleteRowLink = `${this.dropdownToggleMenu} a[data-url*='/delete']`;
  }

  /* Header Methods */
  /**
   * Go to languages page
   * @return {Promise<void>}
   */
  async goToSubTabLanguages() {
    await this.clickAndWaitForNavigation(this.languagesNavItemLink);
  }

  /**
   * Go to localization page
   * @return {Promise<void>}
   */
  async goToSubTabLocalization() {
    await this.clickAndWaitForNavigation(this.localizationNavItemLink);
  }

  /**
   * Go to geolocation page
   * @return {Promise<void>}
   */
  async goToSubTabGeolocation() {
    await this.clickAndWaitForNavigation(this.geolocationNavItemLink);
  }

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
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn.replace('%FILTERBY', filterBy), value ? 'Yes' : 'No');
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
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }

  /**
   * Get exchange rate value
   * @param row
   * @return {Promise<integer>}
   */
  async getExchangeRateValue(row) {
    return this.getNumberFromText(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', 'conversion_rate'),
    );
  }

  /**
   * Get currency row from table
   * @param row
   * @return {Promise<object>}
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
    return this.elementVisible(
      this.enableColumnValidIcon.replace('%ROW', row),
      100,
    );
  }

  /**
   * Update Enable column for the value wanted in currency list
   * @param row
   * @param valueWanted
   * @return {Promise<boolean>}, true if click has been performed
   */
  async updateEnabledValue(row = 1, valueWanted = true) {
    if (await this.getToggleColumnValue(row) !== valueWanted) {
      await this.clickAndWaitForNavigation(this.enableColumn.replace('%ROW', row));
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
    this.dialogListener(true);
    await Promise.all([
      this.page.click(this.dropdownToggleButton.replace('%ROW', row)),
      this.page.waitForSelector(
        `${this.dropdownToggleButton}[aria-expanded='true']`.replace('%ROW', row),
      ),
    ]);
    await this.clickAndWaitForNavigation(this.deleteRowLink.replace('%ROW', row));
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
