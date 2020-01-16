require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Addresses extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Addresses •';

    // Selectors
    // List of addresses
    this.addressGridPanel = '#address_grid_panel';
    this.addressGridTitle = `${this.addressGridPanel} h3.card-header-title`;
    this.addresssListForm = '#address_grid';
    this.addresssListTableRow = `${this.addresssListForm} tbody tr:nth-child(%ROW)`;
    this.addresssListTableColumn = `${this.addresssListTableRow} td.column-%COLUMN`;
    // Filters
    this.addressFilterColumnInput = `${this.addresssListForm} #address_%FILTERBY`;
    this.filterSearchButton = `${this.addresssListForm} button[name='address[actions][search]']`;
    this.filterResetButton = `${this.addresssListForm} button[name='address[actions][reset]']`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @return {Promise<integer>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
  }

  /**
   * get number of elements in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.addressGridTitle);
  }

  /**
   * Reset Filter And get number of elements in list
   * @return {Promise<integer>}
   */
  async resetAndGetNumberOfLines() {
    await this.resetFilter();
    return this.getNumberOfElementInGrid();
  }

  /**
   * Filter list of addresses
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterAddresses(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.addressFilterColumnInput.replace('%FILTERBY', filterBy), value.toString());
        break;
      case 'select':
        await this.selectByVisibleText(
          this.addressFilterColumnInput.replace('%FILTERBY', filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param row, row in table
   * @param column, which column
   * @return {Promise<string>}
   */
  async getTextColumnFromTableAddresses(row, column) {
    return this.getTextContent(this.addresssListTableColumn.replace('%ROW', row).replace('%COLUMN', column));
  }
};
