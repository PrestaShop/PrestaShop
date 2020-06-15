require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class Stocks extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Stock •';
    this.successfulUpdateMessage = 'Stock successfully updated';

    // Selectors
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
   * @return {Promise<void>}
   */
  async goToSubTabMovements() {
    await this.page.click(this.movementsNavItemLink);
    await this.waitForVisibleSelector(`${this.movementsNavItemLink}.active`);
  }

  /**
   * Get the total number of products
   * @returns {Promise<int>}
   */
  async getTotalNumberOfProducts() {
    await this.waitForVisibleSelector(this.searchButton, 2000);
    await this.page.waitForSelector(this.productListLoading, {state: 'hidden'});
    // If pagination that return number of products in this page
    const pagesLength = await this.getProductsPagesLength();
    if (pagesLength === 0) {
      return (await this.page.$$(this.productRows)).length;
    }
    // Get number of products in all pages
    let numberOfProducts = 0;
    for (let i = pagesLength; i > 0; i--) {
      await this.paginateTo(i);
      numberOfProducts += (await this.page.$$(this.productRows)).length;
    }
    return numberOfProducts;
  }

  /**
   * Get the number of lines in the main table
   * @returns {Promise<int>}
   */
  async getNumberOfProductsFromList() {
    await this.waitForVisibleSelector(this.searchButton, 2000);
    await this.page.waitForSelector(this.productListLoading, {state: 'hidden'});
    return (await this.page.$$(this.productRows)).length;
  }

  /**
   * Get number of products pages stocks page
   * @return {Promise<int>}
   */
  async getProductsPagesLength() {
    return (await this.page.$$(this.paginationListItem)).length;
  }

  /**
   * Paginate to a product page
   * @param pageNumber
   * @return {Promise<void>}
   */
  async paginateTo(pageNumber = 1) {
    await Promise.all([
      this.page.click(this.paginationListItemLink(pageNumber)),
      this.waitForVisibleSelector(this.productListLoading),
    ]);
    await this.page.waitForSelector(this.productListLoading, {state: 'hidden'});
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
    return this.getTotalNumberOfProducts();
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
    await this.page.waitForSelector(this.productListLoading, {state: 'hidden'});
  }

  /**
   * get text from column in table
   * @param row
   * @param column, only 3 column are implemented : name, reference, supplier
   * @return {Promise<integer|textContent>}
   */
  async getTextColumnFromTableStocks(row, column) {
    switch (column) {
      case 'name':
        return this.getTextContent(this.productRowNameColumn(row));
      case 'reference':
        return this.getTextContent(this.productRowReferenceColumn(row));
      case 'supplier':
        return this.getTextContent(this.productRowSupplierColumn(row));
      case 'physical':
        return this.getNumberFromText(this.productRowPhysicalColumn(row));
      case 'reserved':
        return this.getNumberFromText(this.productRowReservedColumn(row));
      case 'available':
        return this.getNumberFromText(this.productRowAvailableColumn(row));
      default:
        throw new Error(`${column} was not find as column in this table`);
    }
  }

  /**
   * Get
   * @param row, row in table
   * @return {Promise<{reserved: (integer), available: (integer), physical: (integer)}>}
   */
  async getStockQuantityForProduct(row) {
    return {
      physical: await (this.getTextColumnFromTableStocks(row, 'physical')),
      reserved: await (this.getTextColumnFromTableStocks(row, 'reserved')),
      available: await (this.getTextColumnFromTableStocks(row, 'available')),
    };
  }

  /**
   * Update Stock value by setting input value
   * @param row, row in table
   * @param value, value to add/subtract from quantity
   * @return {Promise<textContent>}
   */
  async updateRowQuantityWithInput(row, value) {
    await this.setValue(this.productRowQuantityColumnInput(row), value.toString());
    // Wait for check button before click
    await this.waitForSelectorAndClick(this.productRowQuantityUpdateButton(row));
    // Wait for alert-Box after update quantity and close alert-Box
    await this.waitForVisibleSelector(this.alertBoxTextSpan);
    const textContent = await this.getTextContent(this.alertBoxTextSpan);
    await this.page.click(this.alertBoxButtonClose);
    return textContent;
  }

  /**
   * Bulk Edit quantity by setting input value
   * @param value
   * @return {Promise<textContent>}
   */
  async bulkEditQuantityWithInput(value) {
    // Select All products
    await this.page.$eval(this.selectAllCheckbox, el => el.click());
    // Set value in input
    await this.setValue(this.bulkEditQuantityInput, value.toString());
    // Wait for check button before click
    await this.page.click(this.applyNewQuantityButton);
    // Wait for alert-Box after update quantity and close alert-Box
    await this.waitForVisibleSelector(this.alertBoxTextSpan);
    const textContent = await this.getTextContent(this.alertBoxTextSpan);
    await this.page.click(this.alertBoxButtonClose);
    return textContent;
  }

  /**
   * Filter stocks by product's status
   * @param status
   * @return {Promise<void>}
   */
  async filterByStatus(status) {
    await this.openCloseAdvancedFilter();
    switch (status) {
      case 'enabled':
        await this.page.click(this.filterStatusEnabledLabel);
        break;
      case 'disabled':
        await this.page.click(this.filterStatusDisabledLabel);
        break;
      case 'all':
        await this.page.click(this.filterStatusAllLabel);
        break;
      default:
        throw Error(`${status} was not found as an option`);
    }
  }

  /**
   * Filter stocks by product's category
   * @param category
   * @return {Promise<void>}
   */
  async filterByCategory(category) {
    await this.openCloseAdvancedFilter();
    await this.page.click(this.filterCategoryExpandButton);
    await this.page.click(this.filterCategoryCheckBoxDiv(category));
    await this.page.waitForSelector(this.productListLoading, {state: 'hidden'});
    await this.page.click(this.filterCategoryCollapseButton);
    await this.openCloseAdvancedFilter(false);
  }

  /**
   * Open / close advanced filter
   * @param toOpen
   * @return {Promise<void>}
   */
  async openCloseAdvancedFilter(toOpen = true) {
    await Promise.all([
      this.page.click(this.advancedFiltersButton),
      this.waitForVisibleSelector(`${this.advancedFiltersButton}[aria-expanded='${toOpen.toString()}']`),
    ]);
  }
};
