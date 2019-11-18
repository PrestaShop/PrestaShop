require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Stocks extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';

    // Selectors
    this.MovementNavItemLink = '#head_tabs li:nth-child(2) > a';
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;
    // tags
    this.searchTagsList = 'form.search-form div.tags-wrapper span.tag';
    this.searchTagsListCloseSpan = `${this.searchTagsList} i`;


    this.productList = 'table.table';
    this.productRow = `${this.productList} tbody tr:nth-child(%ROW)`;
    this.productRowNameColumn = `${this.productRow} td:nth-child(1) div.media-body p`;
    this.productRowReferenceColumn = `${this.productRow} td:nth-child(2)`;
    this.productRowSupplierColumn = `${this.productRow} td:nth-child(3)`;
    this.productRowPhysicalColumn = `${this.productRow} td:nth-child(5)`;
    this.productRowAvailableColumn = `${this.productRow} td:nth-child(7)`;

    // loader
    this.productListLoading = `${this.productRow.replace('%ROW', 1)} td:nth-child(1) div.ps-loader`;
  }

  /*
  Methods
   */

  /**
   * Change Tab to Movements in Stock Page
   * @return {Promise<void>}
   */
  async goToSubTabMovements() {
    await this.page.click(this.MovementNavItemLink, {waitUntil: 'networkidle2'});
  }

  /**
   * Get the number of lines in the main table
   * @returns {Promise<*>}
   */
  async getNumberOfProductsFromList() {
    await this.page.waitForSelector(this.productListLoading, {hidden: true});
    return (await this.page.$$(this.productRow.replace('%ROW', 1))).length;
  }

  /**
   * Remove all filter tags in the basic search input
   * @returns {Promise<void>}
   */
  async resetFilter() {
    const closeButtons = await this.page.$$(this.searchTagsListCloseSpan);
    /* eslint-disable no-restricted-syntax */
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }
    /* eslint-enable no-restricted-syntax */
    return this.getNumberOfProductsFromList();
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
      this.page.waitForSelector(this.productListLoading),
    ]);
    await this.page.waitForSelector(this.productListLoading, {hidden: true});
  }

  /**
   * get text from column in table
   * @param row
   * @param column, only 3 column are implemented : name, reference, supplier
   * @return {Promise<textContent>}
   */
  async getTextColumnFromTableStocks(row, column) {
    switch (column) {
      case 'name':
        return this.getTextContent(this.productRowNameColumn.replace('%ROW', row));
      case 'reference':
        return this.getTextContent(this.productRowReferenceColumn.replace('%ROW', row));
      case 'supplier':
        return this.getTextContent(this.productRowSupplierColumn.replace('%ROW', row));
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }
};
