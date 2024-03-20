// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';

/**
 * Category page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CategoryPage extends FOBasePage {
  public readonly messageAddedToWishlist: string;

  private readonly bodySelector: string;

  private readonly mainSection: string;

  protected headerNamePage: string;

  private readonly totalProducts: string;

  private readonly productsSection: string;

  private readonly productListTop: string;

  protected productListDiv: string;

  private readonly pagesList: string;

  protected productItemListDiv: string;

  protected paginationText: string;

  protected paginationNext: string;

  private readonly paginationPrevious: string;

  private readonly sortByDiv: string;

  private readonly sortByButton: string;

  private readonly valueToSortBy: (sortBy: string) => string;

  protected sideBlockCategories: string;

  protected sideBlockCategoriesItem: string;

  protected sideBlockCategory: (text: string) => string;

  private readonly subCategoriesList: string;

  private readonly subCategoriesItem: (title: string) => string;

  private readonly productList: string;

  protected productArticle: (number: number) => string;

  private readonly productTitle: (number: number) => string;

  protected productPrice: (number: number) => string;

  private readonly productAttribute: (number: number, attribute: string) => string;

  protected productImg: (number: number) => string;

  private readonly productDescriptionDiv: (number: number) => string;

  protected productQuickViewLink: (number: number) => string;

  private readonly productAddToWishlist: (number: number) => string;

  private readonly categoryDescription: string;

  protected searchFilters: string;

  private readonly searchFilter: (facetType: string) => string;

  protected searchFiltersCheckbox: (facetType: string) => string;

  private readonly searchFiltersRadio: (facetType: string) => string;

  private readonly searchFiltersDropdown: (facetType: string) => string;

  protected closeOneFilter: (row: number) => string;

  protected searchFiltersSlider: string;

  private readonly searchFilterPriceValues: string;

  protected clearAllFiltersLink: string;

  private readonly activeSearchFilters: string;

  private readonly wishlistModal: string;

  private readonly wishlistModalListItem: string;

  private readonly wishlistToast: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on category page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    // Message
    this.messageAddedToWishlist = 'Product added';

    // Selectors
    this.bodySelector = '#category';
    this.mainSection = '#main';
    this.headerNamePage = '#js-product-list-header';
    this.totalProducts = '#js-product-list-top .total-products > p';
    this.productsSection = '#products';
    this.productListTop = '#js-product-list-top';
    this.productListDiv = '#js-product-list';
    this.productItemListDiv = `${this.productListDiv} .products div.product`;
    this.sortByDiv = `${this.productsSection} div.sort-by-row`;
    this.sortByButton = `${this.sortByDiv} button.select-title`;
    this.valueToSortBy = (sortBy: string) => `${this.productListTop} .products-sort-order .dropdown-menu a[href*='${sortBy}']`;

    // Categories SideBlock
    this.sideBlockCategories = '.block-categories';
    this.sideBlockCategoriesItem = `${this.sideBlockCategories} ul.category-sub-menu li`;
    this.sideBlockCategory = (text: string) => `${this.sideBlockCategoriesItem} a:text("${text}")`;

    // SubCategories List
    this.subCategoriesList = '#subcategories ul.subcategories-list';
    this.subCategoriesItem = (title: string) => `${this.subCategoriesList} li a[title="${title}"]`;

    // Products list
    this.productList = '#js-product-list';
    this.productArticle = (number: number) => `${this.productList} .products div:nth-child(${number}) article`;

    this.productTitle = (number: number) => `${this.productArticle(number)} .product-title`;
    this.productPrice = (number: number) => `${this.productArticle(number)} span.price`;
    this.productAttribute = (number: number, attribute: string) => `${this.productArticle(number)} .product-${attribute}`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = (number: number) => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} a.quick-view`;
    this.productAddToWishlist = (number: number) => `${this.productArticle(number)} button.wishlist-button-add`;

    // Pagination selectors
    this.pagesList = '.page-list';
    this.paginationText = `${this.productListDiv} .pagination div:nth-child(1)`;
    this.paginationNext = '#js-product-list nav.pagination a[rel=\'next\']';
    this.paginationPrevious = '#js-product-list nav.pagination a[rel=\'prev\']';

    this.categoryDescription = '#category-description';

    // Filter
    this.searchFilters = '#search_filters';
    this.searchFilter = (facetType: string) => `${this.searchFilters} section[data-type="${facetType}"] ul[id^="facet"]`;
    this.searchFiltersCheckbox = (facetType: string) => `${this.searchFilter(facetType)} label.facet-label `
      + 'input[type="checkbox"]';
    this.searchFiltersRadio = (facetType: string) => `${this.searchFilter(facetType)} label.facet-label input[type="radio"]`;
    this.searchFiltersDropdown = (facetType: string) => `${this.searchFilter(facetType)} .facet-dropdown`;
    this.searchFiltersSlider = '.ui-slider-horizontal';
    this.searchFilterPriceValues = '[id*=facet_label]';
    this.clearAllFiltersLink = '#_desktop_search_filters_clear_all button.js-search-filters-clear-all';
    this.activeSearchFilters = '#js-active-search-filters';
    this.closeOneFilter = (row: number) => `#js-active-search-filters ul li:nth-child(${row}) a i`;

    // Wishlist
    this.wishlistModal = '.wishlist-add-to .wishlist-modal.show';
    this.wishlistModalListItem = `${this.wishlistModal} ul.wishlist-list li.wishlist-list-item:nth-child(1)`;
    this.wishlistToast = '.wishlist-toast .wishlist-toast-text';
  }

  /* Methods */
  /**
   * Check if user is in category page
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCategoryPage(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.bodySelector, 2000);
  }

  /**
   * Get products number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.totalProducts);
  }

  /**
   * Get number of products displayed in category page
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfProductsDisplayed(page: Page): Promise<number> {
    return page.locator(this.productItemListDiv).count();
  }

  /**
   * Get number of all products
   * @param page {Page}
   * @returns {Promise<number>}
   */
  async getNumberOfProducts(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.productListTop);
  }

  /**
   * Get the header name of the page
   * @param page {Page}
   * @returns {Promise<object>}
   */
  async getHeaderPageName(page: Page): Promise<object> {
    return page.locator(this.headerNamePage).innerText().valueOf();
  }

  /**
   * Get sort by value from button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getSortByValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.sortByButton);
  }

  /**
   * Is Sort By Button Visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isSortButtonVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.sortByButton, 1000);
  }

  /**
   * Sort products list
   * @param page {Page} Browser tab
   * @param sortBy {string} Value to sort by
   * @return {Promise<void>}
   */
  async sortProductsList(page: Page, sortBy: string): Promise<void> {
    await this.scrollTo(page, this.sortByButton);
    await this.waitForSelectorAndClick(page, this.sortByButton);
    await this.waitForVisibleSelector(page, `${this.sortByButton}[aria-expanded="true"]`);
    await this.waitForSelectorAndClick(page, this.valueToSortBy(sortBy));
    await page.waitForTimeout(3000);
  }

  /**
   * Get all products attribute
   * @param page {Page} Browser tab
   * @param attribute {string} Attribute to get
   * @returns {Promise<string[]>}
   */
  async getAllProductsAttribute(page: Page, attribute: string): Promise<string[]> {
    let rowContent: string;
    const rowsNumber: number = await this.getNumberOfProducts(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      if (attribute === 'price-and-shipping') {
        rowContent = await this.getTextContent(page, `${this.productAttribute(i, attribute)} span.price`);
      } else {
        rowContent = await this.getTextContent(page, this.productAttribute(i, attribute));
      }
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Is pages list visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isPagesListVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.pagesList);
  }

  /**
   * Get pages list
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getPagesList(page: Page): Promise<string> {
    return this.getTextContent(page, this.pagesList);
  }

  /**
   * Get showing Items
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getShowingItems(page: Page): Promise<string> {
    return this.getTextContent(page, this.paginationText, true);
  }

  /**
   * Go to the next page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToNextPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.paginationNext);
  }

  /**
   * Go to previous page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToPreviousPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.paginationPrevious);
  }

  /**
   * Go to product page
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @returns {Promise<void>}
   */
  async goToProductPage(page: Page, id: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.productAttribute(id, 'thumbnail'));
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).hover();
    let displayed: boolean = false;

    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        (selector) => {
          const element: HTMLElement | null = document.querySelector(selector);

          if (element === null) {
            return false;
          }
          return window.getComputedStyle(element, ':after').getPropertyValue('display') === 'block';
        },
        this.productDescriptionDiv(id),
      );
      await page.waitForTimeout(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.waitForVisibleSelector(page, quickViewModal.quickViewModalDiv),
      page.locator(this.productQuickViewLink(id)).evaluate((el: HTMLElement) => el.click()),
    ]);
  }

  /**
   * Get category description
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getCategoryDescription(page: Page): Promise<string> {
    return this.getTextContent(page, this.categoryDescription, true);
  }

  /**
   * Returns the URL of the main image of a subcategory
   * @param page {Page} Browser tab
   * @param name {string} Name of a category
   * @returns {Promise<string|null>}
   */
  async getCategoryImageMain(page: Page, name: string): Promise<string | null> {
    return this.getAttributeContent(page, `${this.subCategoriesItem(name)} source`, 'srcset');
  }

  /**
   * Returns the position of a specific product in a list
   * @param page {Page} Browser tab
   * @param idProduct {number} ID of a product
   * @return {Promise<number|null>}
   */
  async getNThChildFromIDProduct(page: Page, idProduct: number): Promise<number | null> {
    const productItemsLength = await this.getNumberOfProductsDisplayed(page);

    for (let idx: number = 1; idx <= productItemsLength; idx++) {
      const attributeIdProduct = await this.getAttributeContent(page, this.productArticle(idx), 'data-id-product');

      if (attributeIdProduct) {
        if (idProduct === parseInt(attributeIdProduct, 10)) {
          return idx;
        }
      }
    }

    return null;
  }

  ////////////////////////////
  // Side Block : Categories
  ////////////////////////////
  /**
   * Return if Side Block : Categories is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasBlockCategories(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.sideBlockCategories, 1000);
  }

  /**
   * Return if the number of categories in side block
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumBlockCategories(page: Page): Promise<number> {
    return page.locator(this.sideBlockCategoriesItem).count();
  }

  /**
   * Click on the category in side block
   * @param page {Page} Browser tab
   * @param categoryName {string}
   * @return {Promise<void>}
   */
  async clickBlockCategory(page: Page, categoryName: string): Promise<void> {
    await this.clickAndWaitForURL(page, this.sideBlockCategory(categoryName));
  }

  /////////////////////////
  // Side Block : Filters
  /////////////////////////
  /**
   * Return if search filters are visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasSearchFilters(page: Page): Promise<boolean> {
    return (await page.locator(this.searchFilters).count()) !== 0;
  }

  /**
   * Return if search filters use checkbox button
   * @param page {Page} Browser tab
   * @param facetType {string} Facet type
   * @return {Promise<boolean>}
   */
  async isSearchFiltersCheckbox(page: Page, facetType: string): Promise<boolean> {
    return (await page.locator(this.searchFiltersCheckbox(facetType)).count()) !== 0;
  }

  /**
   * Filter by checkbox
   * @param page {Page} Browser tab
   * @param facetType {string} Type of filter
   * @param checkboxName {string} Checkbox name
   * @param toEnable {boolean} True if we need to enable
   * @return {Promise<void>}
   */
  async filterByCheckbox(page: Page, facetType: string, checkboxName: string, toEnable: boolean): Promise<void> {
    await this.setChecked(
      page,
      `${this.searchFiltersCheckbox(facetType)}[data-search-url*=${checkboxName}]`,
      toEnable,
      true,
    );
    await page.waitForTimeout(2000);
  }

  /**
   * Get active filters
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getActiveFilters(page: Page): Promise<string> {
    return this.getTextContent(page, this.activeSearchFilters);
  }

  /**
   * Get product href
   * @param page {Page} Browser tab
   * @param productRow {number} Product row
   * @return {Promise<string>}
   */
  async getProductHref(page: Page, productRow: number): Promise<string> {
    return this.getAttributeContent(page, `${this.productArticle(productRow)} div.thumbnail-top a`, 'href');
  }

  /**
   * Get product price
   * @param page {Page} Browser tab
   * @param productRow {number} Product row
   * @return {Promise<number>}
   */
  async getProductPrice(page: Page, productRow: number): Promise<number> {
    return this.getNumberFromText(page, this.productPrice(productRow));
  }

  /**
   * Clear all filters
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async clearAllFilters(page: Page): Promise<boolean> {
    await page.locator(this.clearAllFiltersLink).click();

    return this.elementNotVisible(page, this.activeSearchFilters, 2000);
  }

  /**
   * Close filter
   * @param page {Page} Browser tab
   * @param row {number} Row of the filter
   * @return {Promise<void>}
   */
  async closeFilter(page: Page, row: number): Promise<void> {
    await page.locator(this.closeOneFilter(row)).click();
  }

  /**
   * Is active filter not visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isActiveFilterNotVisible(page: Page): Promise<boolean> {
    return this.elementNotVisible(page, this.activeSearchFilters, 2000);
  }

  /**
   * Get maximum price from slider
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getMaximumPrice(page: Page): Promise<number> {
    const test = await this.getTextContent(page, this.searchFilterPriceValues);

    return (parseInt(test.split('€')[2], 10));
  }

  /**
   * Get minimum price from slider
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getMinimumPrice(page: Page): Promise<number> {
    const test = await this.getTextContent(page, this.searchFilterPriceValues);

    return (parseInt(test.split('€')[1], 10));
  }

  /**
   * Filter by price
   * @param page {Page} Browser tab
   * @param minPrice {number} Minimum price in the slider
   * @param maxPrice {number} Maximum price in the slider
   * @param filterFrom {number} The minimum value to filter
   * @param filterTo {number} The maximum value to filter
   * @return {Promise<void>}
   */
  async filterByPrice(page: Page, minPrice: number, maxPrice: number, filterFrom: number, filterTo: number): Promise<void> {
    const sliderTrack = await page.locator(this.searchFiltersSlider);
    const sliderOffsetWidth = await sliderTrack.evaluate((el) => el.getBoundingClientRect().width);
    const pxOneEuro = sliderOffsetWidth / (maxPrice - minPrice);

    await sliderTrack.hover({force: true, position: {x: ((filterFrom - minPrice) * pxOneEuro), y: 0}});
    await page.mouse.down();
    await page.mouse.up();
    await page.waitForTimeout(2000);
    await sliderTrack.hover({force: true, position: {x: (filterTo - minPrice) * pxOneEuro, y: 0}});
    await page.mouse.down();
    await page.mouse.up();
    await page.waitForTimeout(2000);
  }

  /**
   * Return if search filters use radio button
   * @param page {Page} Browser tab
   * @param facetType {string} Facet type
   * @return {Promise<boolean>}
   */
  async isSearchFilterRadio(page: Page, facetType: string): Promise<boolean> {
    return (await page.locator(this.searchFiltersRadio(facetType)).count()) !== 0;
  }

  /**
   * Return if search filters use radio button
   * @param page {Page} Browser tab
   * @param facetType {string} Facet type
   * @return {Promise<boolean>}
   */
  async isSearchFilterDropdown(page: Page, facetType: string): Promise<boolean> {
    return (await page.locator(this.searchFiltersDropdown(facetType)).count()) !== 0;
  }

  /**
   * Add a product (based on its index) to the first wishlist
   * @param page {Page}
   * @param idxProduct {number}
   * @returns Promise<string>
   */
  async addToWishList(page: Page, idxProduct: number): Promise<string> {
    if (!(await this.isAddedToWishlist(page, idxProduct))) {
      // Click on the heart
      await page.locator(this.productAddToWishlist(idxProduct)).click();
      // Wait for the modal
      await this.elementVisible(page, this.wishlistModal, 3000);
      // Click on the first wishlist
      await page.locator(this.wishlistModalListItem).click();
      // Wait for the toast
      await this.elementVisible(page, this.wishlistToast, 3000);

      return this.getTextContent(page, this.wishlistToast);
    }

    // Already added
    return this.messageAddedToWishlist;
  }

  /**
   * Check if a product (based on its index) is added to a wishlist
   * @param page {Page}
   * @param idxProduct {number}
   * @returns Promise<boolean>
   */
  async isAddedToWishlist(page: Page, idxProduct: number): Promise<boolean> {
    await page.waitForTimeout(1000);

    return ((await this.getTextContent(page, this.productAddToWishlist(idxProduct))) === 'favorite');
  }
}

const categoryPage = new CategoryPage();
export {categoryPage, CategoryPage};
