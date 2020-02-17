require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Order extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Orders •';

    // Selectors grid panel
    this.gridPanel = '#order_grid_panel';
    this.gridTable = '#order_grid_table';
    this.gridHeaderTitle = `${this.gridPanel} h3.card-header-title`;
    // Filters
    this.filterColumn = `${this.gridTable} #order_%FILTERBY`;
    this.filterSearchButton = `${this.gridTable} button[name='order[actions][search]']`;
    this.filterResetButton = `${this.gridTable} button[name='order[actions][reset]']`;
    // Table rows and columns
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRow = `${this.tableBody} tr:nth-child(%ROW)`;
    this.tableEmptyRow = `${this.tableBody} tr.empty_row`;
    this.tableColumn = `${this.tableRow} td.column-%COLUMN`;
    // Column actions selectors
    this.actionsColumn = `${this.tableRow} td.column-actions`;
    this.viewRowLink = `${this.actionsColumn} a[data-original-title='View']`;
  }

  /*
  Methods
   */
  /**
   * Filter Orders
   * @param filterType
   * @param filterBy
   * @param value
   * @return {Promise<void>}
   */
  async filterOrders(filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(this.filterColumn.replace('%FILTERBY', filterBy), value);
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(this.filterSearchButton);
  }

  /**
   * Reset filter in orders
   * @return {Promise<void>}
   */
  async resetFilter() {
    if (!(await this.elementNotVisible(this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(this.filterResetButton);
    }
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
   * Get number of orders in grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return this.getNumberFromText(this.gridHeaderTitle);
  }

  /**
   * Go to orders Page
   * @param orderRow
   * @return {Promise<void>}
   */
  async goToOrder(orderRow) {
    await this.clickAndWaitForNavigation(this.viewRowLink.replace('%ROW', orderRow));
  }

  /**
   * Get text from Column
   * @param columnName
   * @param row
   * @return {Promise<textContent>}
   */
  async getTextColumn(columnName, row) {
    return this.getTextContent(
      this.tableColumn
        .replace('%ROW', row)
        .replace('%COLUMN', columnName),
    );
  }
};
