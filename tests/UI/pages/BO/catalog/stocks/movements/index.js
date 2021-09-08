require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Movements extends BOBasePage {
  constructor() {
    super();

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
    this.tableRow = row => `${this.tableRows}:nth-child(${row})`;
    this.tableProductNameColumn = row => `${this.tableRow(row)} td:nth-child(1) div.media-body p`;
    this.tableProductReferenceColumn = row => `${this.tableRow(row)} td:nth-child(2)`;
    this.tableQuantityColumn = row => `${this.tableRow(row)} td:nth-child(4) span.qty-number`;
    // Loader
    this.productListLoading = `${this.tableRow(1)} td:nth-child(1) div.ps-loader`;
  }

  /* Header methods */
  /**
   * Go to stocks page
   * @param page
   * @return {Promise<void>}
   */
  async goToSubTabStocks(page) {
    await page.click(this.stocksNavItemLink);
    await this.waitForVisibleSelector(page, `${this.stocksNavItemLink}.active`);
  }

  /**
   * Filter by a word
   * @param page
   * @param value
   * @returns {Promise<void>}
   */
  async simpleFilter(page, value) {
    await page.type(this.searchInput, value);
    await Promise.all([
      page.click(this.searchButton),
      this.waitForVisibleSelector(page, this.productListLoading),
    ]);
    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /* Table methods */
  /**
   * Get text from column in table
   * @param page
   * @param row
   * @param column
   * @return {Promise<string|number>}
   */
  async getTextColumnFromTable(page, row, column) {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.tableProductNameColumn(row));
      case 'reference':
        return this.getTextContent(page, this.tableProductReferenceColumn(row));
      case 'quantity':
        return parseFloat(
          (await this.getTextContent(page, this.tableQuantityColumn(row))).replace(' ', ''),
        );
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get number of element in movements grid
   * @param page
   * @return {Promise<integer>}
   */
  async getNumberOfElementInGrid(page) {
    return (await page.$$(this.tableRows)).length;
  }
}

module.exports = new Movements();
