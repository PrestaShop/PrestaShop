require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class LogsSettings extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Logs •';

    // List of customers
    this.gridPanel = '#logs_grid_panel';
    this.gridTitle = `${this.gridPanel} h3.card-header-title`;
    this.listForm = '#logs_grid';
    this.listTableRow = row => `${this.listForm} tbody tr:nth-child(${row})`;
    this.listTableColumn = (row, column) => `${this.listTableRow(row)} td.column-${column}`;

    // Filters
    this.filterColumnInput = filterBy => `${this.listForm} #logs_${filterBy}`;
    this.filterSearchButton = `${this.listForm} .grid-search-button`;
    this.filterResetButton = `${this.listForm} .grid-reset-button`;
  }

  /*
  Methods
   */
  /**
   * Reset input filters
   * @param page
   * @returns {Promise<void>}
   */
  async resetFilter(page) {
    if (!(await this.elementNotVisible(page, this.filterResetButton, 2000))) {
      await this.clickAndWaitForNavigation(page, this.filterResetButton);
    }
  }

  /**
   * Get number of elements in grid
   * @param page
   * @returns {Promise<number>}
   */
  async getNumberOfElementInGrid(page) {
    return this.getNumberFromText(page, this.gridTitle);
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

  /**
   * Filter list of logs
   * @param page
   * @param filterType, input or select to choose method of filter
   * @param filterBy, column to filter
   * @param value, value to filter with
   * @return {Promise<void>}
   */
  async filterLogs(page, filterType, filterBy, value = '') {
    switch (filterType) {
      case 'input':
        await this.setValue(page, this.filterColumnInput(filterBy), value);
        break;
      case 'select':
        await this.selectByVisibleText(
          page,
          this.filterColumnInput(filterBy),
          value,
        );
        break;
      default:
      // Do nothing
    }
    // click on search
    await this.clickAndWaitForNavigation(page, this.filterSearchButton);
  }

  /**
   * get text from a column
   * @param page
   * @param row, row in table
   * @param column, which column
   * @returns {Promise<string>}
   */
  async getTextColumn(page, row, column) {
    return this.getTextContent(page, this.listTableColumn(row, column));
  }
}

module.exports = new LogsSettings();
