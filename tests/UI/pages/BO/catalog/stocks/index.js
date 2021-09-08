require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * stocks page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Stocks extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add currency page
   */
  constructor() {
    super();

    this.pageTitle = 'Stock •';
    this.successfulUpdateMessage = 'Stock successfully updated';

    // Selectors
    // Alert Box
    this.alertBoxBlock = 'div.alert-box';
    this.alertBoxTextSpan = `${this.alertBoxBlock} p.alert-text span`;
    this.alertBoxButtonClose = `${this.alertBoxBlock} button.close`;

    // Search
    this.movementsNavItemLink = '#head_tabs li:nth-child(2) > a';
    this.searchForm = 'form.search-form';
    this.searchInput = `${this.searchForm} input.input`;
    this.searchButton = `${this.searchForm} button.search-button`;

    // tags
    this.searchTagsList = 'form.search-form div.tags-wrapper span.tag';
    this.searchTagsListCloseSpan = `${this.searchTagsList} i`;

    // Bulk actions
    this.selectAllCheckbox = '#bulk-action + i';
    this.bulkEditQuantityInput = 'div.bulk-qty input';
    this.applyNewQuantityButton = 'button.update-qty';
    this.productList = 'table.table';
    this.productRows = `${this.productList} tbody tr`;
    this.productRow = row => `${this.productRows}:nth-child(${row})`;
    this.productRowNameColumn = row => `${this.productRow(row)} td:nth-child(1) div.media-body p`;
    this.productRowReferenceColumn = row => `${this.productRow(row)} td:nth-child(2)`;
    this.productRowSupplierColumn = row => `${this.productRow(row)} td:nth-child(3)`;
    this.productRowPhysicalColumn = row => `${this.productRow(row)} td:nth-child(5)`;
    this.productRowReservedColumn = row => `${this.productRow(row)} td:nth-child(6)`;
    this.productRowAvailableColumn = row => `${this.productRow(row)} td:nth-child(7)`;

    // Quantity column
    this.productRowQuantityColumn = row => `${this.productRow(row)} td.qty-spinner`;
    this.productRowQuantityColumnInput = row => `${this.productRowQuantityColumn(row)} div.edit-qty input`;
    this.productRowQuantityUpdateButton = row => `${this.productRowQuantityColumn(row)} button.check-button`;

    // loader
    this.productListLoading = `${this.productRows} td:nth-child(1) div.ps-loader`;

    // Filters containers
    this.filtersContainerDiv = '#filters-container';
    this.advancedFiltersButton = `${this.filtersContainerDiv} button[data-target='#filters']`;
    this.filterStatusEnabledLabel = '#enable + label';
    this.filterStatusDisabledLabel = '#disable + label';
    this.filterStatusAllLabel = '#all + label';

    // Filter category
    this.filterCategoryDiv = `${this.filtersContainerDiv} div.filter-categories`;
    this.filterCategoryExpandButton = `${this.filterCategoryDiv} button:nth-child(1)`;
    this.filterCategoryCollapseButton = `${this.filterCategoryDiv} button:nth-child(2)`;
    this.filterCategoryTreeItems = category => `${this.filterCategoryDiv} div.ps-tree-items[label='${category}']`;
    this.filterCategoryCheckBoxDiv = category => `${this.filterCategoryTreeItems(category)} .md-checkbox`;

    // Pagination
    this.paginationList = 'nav ul.pagination';
    this.paginationListItem = `${this.paginationList} li.page-item`;
    this.paginationListItemLink = id => `${this.paginationListItem}:nth-child(${id}) a`;
  }

  /*
  Methods
   */

  /**
   * Change Tab to Movements in Stock Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabMovements(page) {
    await page.click(this.movementsNavItemLink);
    await this.waitForVisibleSelector(page, `${this.movementsNavItemLink}.active`);
  }

  /**
   * Get the total number of products
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getTotalNumberOfProducts(page) {
    await this.waitForVisibleSelector(page, this.searchButton, 2000);
    await this.waitForHiddenSelector(page, this.productListLoading);
    // If pagination that return number of products in this page
    const pagesLength = await this.getProductsPagesLength(page);

    if (pagesLength === 0) {
      return (await page.$$(this.productRows)).length;
    }
    // Get number of products in all pages
    let numberOfProducts = 0;

    for (let i = pagesLength; i > 0; i--) {
      await this.paginateTo(page, i);
      numberOfProducts += (await page.$$(this.productRows)).length;
    }

    return numberOfProducts;
  }

  /**
   * Get the number of lines in the main table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductsFromList(page) {
    await this.waitForVisibleSelector(page, this.searchButton, 2000);
    await this.waitForHiddenSelector(page, this.productListLoading);
    return (await page.$$(this.productRows)).length;
  }

  /**
   * Get number of products pages stocks page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductsPagesLength(page) {
    return (await page.$$(this.paginationListItem)).length;
  }

  /**
   * Paginate to a product page
   * @param page {Page} Browser tab
   * @param pageNumber {number} Value of page to go
   * @return {Promise<void>}
   */
  async paginateTo(page, pageNumber = 1) {
    await Promise.all([
      page.click(this.paginationListItemLink(pageNumber)),
      this.waitForVisibleSelector(page, this.productListLoading),
    ]);
    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /**
   * Remove all filter tags in the basic search input
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetFilter(page) {
    const closeButtons = await page.$$(this.searchTagsListCloseSpan);

    /* eslint-disable no-restricted-syntax */
    for (const closeButton of closeButtons) {
      await closeButton.click();
    }

    /* eslint-enable no-restricted-syntax */
    return this.getTotalNumberOfProducts(page);
  }

  /**
   * Filter by a word
   * @param page {Page} Browser tab
   * @param value {string} Value to st on filter input
   * @returns {Promise<void>}
   */
  async simpleFilter(page, value) {
    await page.type(this.searchInput, value);

    await Promise.all([
      page.click(this.searchButton),
      this.waitForVisibleSelector(page, this.productListLoading, 10000),
    ]);

    await this.waitForHiddenSelector(page, this.productListLoading);
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<number|string>}
   */
  async getTextColumnFromTableStocks(page, row, column) {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.productRowNameColumn(row));
      case 'reference':
        return this.getTextContent(page, this.productRowReferenceColumn(row));
      case 'supplier':
        return this.getTextContent(page, this.productRowSupplierColumn(row));
      case 'physical':
        return this.getNumberFromText(page, this.productRowPhysicalColumn(row));
      case 'reserved':
        return this.getNumberFromText(page, this.productRowReservedColumn(row));
      case 'available':
        return this.getNumberFromText(page, this.productRowAvailableColumn(row));
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get stocks quantities for a product
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @return {Promise<{reserved: number, available: number, physical: number}>}
   */
  async getStockQuantityForProduct(page, row) {
    return {
      physical: await (this.getTextColumnFromTableStocks(page, row, 'physical')),
      reserved: await (this.getTextColumnFromTableStocks(page, row, 'reserved')),
      available: await (this.getTextColumnFromTableStocks(page, row, 'available')),
    };
  }

  /**
   * Update Stock value by setting input value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param quantity {number} Value to add/subtract from quantity
   * @returns {Promise<string>}
   */
  async updateRowQuantityWithInput(page, row, quantity) {
    await this.setValue(page, this.productRowQuantityColumnInput(row), quantity);

    // Wait for check button before click
    await this.waitForSelectorAndClick(page, this.productRowQuantityUpdateButton(row));

    // Wait for alert-Box after update quantity and close alert-Box
    await this.waitForVisibleSelector(page, this.alertBoxTextSpan);
    const textContent = await this.getTextContent(page, this.alertBoxTextSpan);
    await page.click(this.alertBoxButtonClose);

    return textContent;
  }

  /**
   * Bulk edit quantity by setting input value
   * @param page {Page} Browser tab
   * @param quantity {number} Value of quantity to set on input
   * @returns {Promise<string>}
   */
  async bulkEditQuantityWithInput(page, quantity) {
    // Select All products
    await page.$eval(this.selectAllCheckbox, el => el.click());

    // Set value in input
    await this.setValue(page, this.bulkEditQuantityInput, quantity);

    // Wait for check button before click
    await page.click(this.applyNewQuantityButton);

    // Wait for alert-Box after update quantity and close alert-Box
    await this.waitForVisibleSelector(page, this.alertBoxTextSpan);
    const textContent = await this.getTextContent(page, this.alertBoxTextSpan);
    await page.click(this.alertBoxButtonClose);

    return textContent;
  }

  /**
   * Filter stocks by product's status
   * @param page {Page} Browser tab
   * @param status {string} Value of status to set on filter
   * @return {Promise<void>}
   */
  async filterByStatus(page, status) {
    await this.openCloseAdvancedFilter(page);
    switch (status) {
      case 'enabled':
        await page.click(this.filterStatusEnabledLabel);
        break;
      case 'disabled':
        await page.click(this.filterStatusDisabledLabel);
        break;
      case 'all':
        await page.click(this.filterStatusAllLabel);
        break;
      default:
        throw Error(`${status} was not found as an option`);
    }
  }

  /**
   * Filter stocks by product's category
   * @param page {Page} Browser tab
   * @param category {string} Category name to set on filter input
   * @return {Promise<void>}
   */
  async filterByCategory(page, category) {
    await this.openCloseAdvancedFilter(page);
    await page.click(this.filterCategoryExpandButton);
    await page.click(this.filterCategoryCheckBoxDiv(category));
    await this.waitForHiddenSelector(page, this.productListLoading);
    await page.click(this.filterCategoryCollapseButton);
    await this.openCloseAdvancedFilter(page, false);
  }

  /**
   * Open / close advanced filter
   * @param page {Page} Browser tab
   * @param toOpen {boolean} True if we need to open advanced filter, false if not
   * @return {Promise<void>}
   */
  async openCloseAdvancedFilter(page, toOpen = true) {
    await Promise.all([
      page.click(this.advancedFiltersButton),
      this.waitForVisibleSelector(page, `${this.advancedFiltersButton}[aria-expanded='${toOpen.toString()}']`),
    ]);
  }
}

module.exports = new Stocks();
