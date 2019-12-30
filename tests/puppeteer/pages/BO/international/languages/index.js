require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Languages extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Languages •';

    // Header Selectors
    this.localizationNavItemLink = '#subtab-AdminCurrencies';
    this.geolocationNavItemLink = '#subtab-AdminGeolocation';
    this.currenciesNavItemLink = '#subtab-AdminCurrencies';
    // Selectors grid panel
    this.gridPanel = '#language_grid_panel';
    this.gridTable = '#language_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #language_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='language[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='language[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
  }

  /* Header methods */
  /**
   * Go to currencies page
   * @return {Promise<void>}
   */
  async goToSubTabCurrencies() {
    await this.clickAndWaitForNavigation(this.currenciesNavItemLink);
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

  /* Reset methods */
  /**
   * Reset filters in table
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
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

  /* Filter method */
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
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value.toString());
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

  /* Table methods */
  /**
   * Get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTable(row, column) {
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', column),
    );
  }
};
