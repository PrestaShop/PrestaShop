import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * stocks page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class Stocks extends BOBasePage {
  public readonly pageTitle: string;

  private readonly alertBoxBlock: string;

  private readonly alertBoxTextSpan: string;

  private readonly alertBoxButtonClose: string;

  private readonly movementsNavItemLink: string;

  private readonly searchForm: string;

  private readonly searchInput: string;

  private readonly searchButton: string;

  private readonly searchTagsList: string;

  private readonly searchTagsListCloseSpan: string;

  private readonly selectAllCheckbox: string;

  private readonly bulkEditQuantityInput: string;

  private readonly applyNewQuantityButton: string;

  private readonly productList: string;

  private readonly productRows: string;

  private readonly productRow: (row: number) => string;

  private readonly productRowNameColumn: (row: number) => string;

  private readonly productRowReferenceColumn: (row: number) => string;

  private readonly productRowSupplierColumn: (row: number) => string;

  private readonly productRowPhysicalColumn: (row: number) => string;

  private readonly productRowReservedColumn: (row: number) => string;

  private readonly productRowAvailableColumn: (row: number) => string;

  private readonly productRowQuantityColumn: (row: number) => string;

  private readonly productRowQuantityColumnInput: (row: number) => string;

  private readonly productRowQuantityUpdateButton: (row: number) => string;

  private readonly productListLoading: string;

  private readonly filtersContainerDiv: string;

  private readonly advancedFiltersButton: string;

  private readonly filterStatusEnabledLabel: string;

  private readonly filterStatusDisabledLabel: string;

  private readonly filterStatusAllLabel: string;

  private readonly filterCategoryDiv: string;

  private readonly filterCategoryExpandButton: string;

  private readonly filterCategoryCollapseButton: string;

  private readonly filterCategoryTreeItems: (category: string) => string;

  private readonly filterCategoryCheckBoxDiv: (category: string) => string;

  private readonly paginationList: string;

  private readonly paginationListItem: string;

  private readonly paginationListItemLink: (id: number) => string;

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
    this.productRow = (row: number) => `${this.productRows}:nth-child(${row})`;
    this.productRowNameColumn = (row: number) => `${this.productRow(row)} td[data-role=product-name]`;
    this.productRowReferenceColumn = (row: number) => `${this.productRow(row)} td[data-role=product-reference]`;
    this.productRowSupplierColumn = (row: number) => `${this.productRow(row)} td[data-role=product-supplier-name]`;
    this.productRowPhysicalColumn = (row: number) => `${this.productRow(row)} td[data-role=physical-quantity]`;
    this.productRowReservedColumn = (row: number) => `${this.productRow(row)} td[data-role=reserved-quantity]`;
    this.productRowAvailableColumn = (row: number) => `${this.productRow(row)} td[data-role=available-quantity]`;

    // Quantity column
    this.productRowQuantityColumn = (row: number) => `${this.productRow(row)} td[data-role=update-quantity]`;
    this.productRowQuantityColumnInput = (row: number) => `${this.productRowQuantityColumn(row)} div.edit-qty input`;
    this.productRowQuantityUpdateButton = (row: number) => `${this.productRowQuantityColumn(row)} button.check-button`;

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
    this.filterCategoryTreeItems = (category: string) => `${this.filterCategoryDiv} div.ps-tree-items[label='${category}']`;
    this.filterCategoryCheckBoxDiv = (category: string) => `${this.filterCategoryTreeItems(category)} .md-checkbox`;

    // Pagination
    this.paginationList = 'nav ul.pagination';
    this.paginationListItem = `${this.paginationList} li.page-item`;
    this.paginationListItemLink = (id: number) => `${this.paginationListItem}:nth-child(${id}) a`;
  }

  /*
  Methods
   */

  /**
   * Change Tab to Movements in Stock Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToSubTabMovements(page: Page): Promise<void> {
    await page.click(this.movementsNavItemLink);
    await page.waitForResponse('**/api/stock-movements/**');
    await this.waitForVisibleSelector(page, `${this.movementsNavItemLink}.active`, 2000);
  }

  /**
   * Get the total number of products
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getTotalNumberOfProducts(page: Page): Promise<number> {
    await this.waitForVisibleSelector(page, this.searchButton, 2000);
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
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
  async getNumberOfProductsFromList(page: Page): Promise<number> {
    await this.waitForVisibleSelector(page, this.searchButton, 2000);
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }

    return (await page.$$(this.productRows)).length;
  }

  /**
   * Get number of products pages stocks page
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductsPagesLength(page: Page): Promise<number> {
    return (await page.$$(this.paginationListItem)).length;
  }

  /**
   * Paginate to a product page
   * @param page {Page} Browser tab
   * @param pageNumber {number} Value of page to go
   * @return {Promise<void>}
   */
  async paginateTo(page: Page, pageNumber: number = 1): Promise<void> {
    await page.click(this.paginationListItemLink(pageNumber));
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
  }

  /**
   * Remove all filter tags in the basic search input
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async resetFilter(page: Page): Promise<number> {
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
  async simpleFilter(page: Page, value: string): Promise<void> {
    await page.locator(this.searchInput).fill(value);
    await page.click(this.searchButton);
    if (await this.elementVisible(page, this.productListLoading, 1000)) {
      await this.waitForHiddenSelector(page, this.productListLoading);
    }
  }

  /**
   * Get text from column in table
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param column {string} Column to get text value
   * @return {Promise<number|string>}
   */
  async getTextColumnFromTableStocks(page: Page, row: number, column: string): Promise<string> {
    switch (column) {
      case 'name':
        return this.getTextContent(page, this.productRowNameColumn(row));
      case 'reference':
        return this.getTextContent(page, this.productRowReferenceColumn(row));
      case 'supplier':
        return this.getTextContent(page, this.productRowSupplierColumn(row));
      case 'physical':
        return this.getTextContent(page, this.productRowPhysicalColumn(row));
      case 'reserved':
        return this.getTextContent(page, this.productRowReservedColumn(row));
      case 'available':
        return this.getTextContent(page, this.productRowAvailableColumn(row));
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
  async getStockQuantityForProduct(page: Page, row: number) {
    return {
      physical: parseInt(await (this.getTextColumnFromTableStocks(page, row, 'physical')), 10),
      reserved: parseInt(await (this.getTextColumnFromTableStocks(page, row, 'reserved')), 10),
      available: parseInt(await (this.getTextColumnFromTableStocks(page, row, 'available')), 10),
    };
  }

  /**
   * Update Stock value by setting input value
   * @param page {Page} Browser tab
   * @param row {number} Row on table
   * @param quantity {number} Value to add/subtract from quantity
   * @returns {Promise<string>}
   */
  async updateRowQuantityWithInput(page: Page, row: number, quantity: number): Promise<string> {
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
  async bulkEditQuantityWithInput(page: Page, quantity: number): Promise<string> {
    // Select All products
    await page.$eval(this.selectAllCheckbox, (el: HTMLElement) => el.click());

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
  async filterByStatus(page: Page, status: string): Promise<void> {
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
  async filterByCategory(page: Page, category: string): Promise<void> {
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
  async openCloseAdvancedFilter(page: Page, toOpen: boolean = true): Promise<void> {
    await Promise.all([
      page.click(this.advancedFiltersButton),
      this.waitForVisibleSelector(page, `${this.advancedFiltersButton}[aria-expanded='${toOpen.toString()}']`),
    ]);
  }

  /**
   * @override
   * Open help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page: Page): Promise<boolean> {
    await page.$eval(this.helpButton, (el: HTMLElement) => el.click());
    return this.elementVisible(page, `${this.rightSidebar}.sidebar-open`, 2000);
  }

  /**
   * @override
   * Close help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page: Page): Promise<boolean> {
    await page.$eval(this.helpButton, (el: HTMLElement) => el.click());
    return this.elementVisible(page, `${this.rightSidebar}:not(.sidebar-open)`, 2000);
  }
}

export default new Stocks();
