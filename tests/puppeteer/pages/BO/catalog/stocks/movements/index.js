require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Movements extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';

    // Header selectors
    this.stocksNavItemLink = '#head_tabs li:nth-child(1) > a';
    // Simple filter selectors
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} .search-input input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;
    // Table selectors
    this.gridTable = '.stock-movements table.table';
    this.tableBody = `${this.gridTable} tbody`;
    this.tableRows = `${this.tableBody} tr`;
    this.tableRow = `${this.tableRows}:nth-child(%ROW)`;
    this.tableProductNameColumn = `${this.tableRow} td:nth-child(1) div.media-body p`;
    this.tableProductReferenceColumn = `${this.tableRow} td:nth-child(2)`;
    this.tableQuantityColumn = `${this.tableRow} td:nth-child(4) span.qty-number`;
    // Loader
    this.productListLoading = `${this.tableRow.replace('%ROW', 1)} td:nth-child(1) div.ps-loader`;
  }

  /* Header methods */
  /**
   * Go to stocks page
   * @return {Promise<void>}
   */
  async goToSubTabStocks() {
    await this.page.click(this.stocksNavItemLink);
    await this.waitForVisibleSelector(`${this.stocksNavItemLink}.active`);
  }

  /**
   * Filter by a word
   * @param value
   * @returns {Promise<void>}
   */
  async simpleFilter(value) {
    await this.page.type(this.searchInput, value);
    await Promise.all([
      this.page.click(this.searchButton),
      this.waitForVisibleSelector(this.productListLoading),
    ]);
    await this.page.waitForSelector(this.productListLoading, {hidden: true});
  }

  /* Table methods */
  /**
   * Get text from column in table
   * @param row
   * @param column
   * @return {Promise<string|float>}
   */
  async getTextColumnFromTable(row, column) {
    switch (column) {
      case 'name':
        return this.getTextContent(this.tableProductNameColumn.replace('%ROW', row));
      case 'reference':
        return this.getTextContent(this.tableProductReferenceColumn.replace('%ROW', row));
      case 'quantity':
        return parseFloat(
          (await this.getTextContent(this.tableQuantityColumn.replace('%ROW', row))).replace(' ', ''),
        );
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get number of element in movements grid
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid() {
    return (await this.page.$$(this.tableRows)).length;
  }
};
