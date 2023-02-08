// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Category page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Category extends FOBasePage {
  private readonly bodySelector: string;

  private readonly mainSection: string;

  private readonly headerNamePage: string;

  private readonly productsSection: string;

  private readonly productListTop: string;

  private readonly productListDiv: string;

  private readonly pagesList: string;

  private readonly productItemListDiv: string;

  private readonly paginationText: string;

  private readonly paginationNext: string;

  private readonly paginationPrevious: string;

  private readonly sortByDiv: string;

  private readonly sortByButton: string;

  private readonly valueToSortBy: (sortBy: string) => string;

  private readonly subCategoriesList: string;

  private readonly subCategoriesItem: (title: string) => string;

  private readonly productList: string;

  private readonly productArticle: (number: number) => string;

  private readonly productTitle: (number: number) => string;

  private readonly productPrice: (number: number) => string;

  private readonly productAttribute: (number: number, attribute: string) => string;

  private readonly productImg: (number: number) => string;

  private readonly productDescriptionDiv: (number: number) => string;

  private readonly productQuickViewLink: (number: number) => string;

  private readonly quickViewModalDiv: string;

  private readonly quickViewModalProductImageCover: string;

  private readonly categoryDescription: string;

  private readonly searchFilters: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on category page
   */
  constructor() {
    super();

    // Selectors
    this.bodySelector = '#category';
    this.mainSection = '#main';
    this.headerNamePage = '#js-product-list-header';
    this.productsSection = '#products';
    this.productListTop = '#js-product-list-top';
    this.productListDiv = '#js-product-list';
    this.productItemListDiv = `${this.productListDiv} .products div.product`;
    this.sortByDiv = `${this.productsSection} div.sort-by-row`;
    this.sortByButton = `${this.sortByDiv} button.select-title`;
    this.valueToSortBy = (sortBy: string) => `${this.productListTop} .products-sort-order .dropdown-menu a[href*='${sortBy}']`;

    // SubCategories List
    this.subCategoriesList = '#subcategories ul.subcategories-list';
    this.subCategoriesItem = (title: string) => `${this.subCategoriesList} li a[title="${title}"]`;

    // Products list
    this.productList = '#js-product-list';
    this.productArticle = (number: number) => `${this.productList} .products div:nth-child(${number}) article`;

    this.productTitle = (number: number) => `${this.productArticle(number)} .product-title`;
    this.productPrice = (number: number) => `${this.productArticle(number)} .product-price-and-shipping`;
    this.productAttribute = (number: number, attribute: string) => `${this.productArticle(number)} .product-${attribute}`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = (number: number) => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} a.quick-view`;

    // Pagination selectors
    this.pagesList = '.page-list';
    this.paginationText = `${this.productListDiv} .pagination div:nth-child(1)`;
    this.paginationNext = '#js-product-list nav.pagination a[rel=\'next\']';
    this.paginationPrevious = '#js-product-list nav.pagination a[rel=\'prev\']';

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewModalProductImageCover = `${this.quickViewModalDiv} div.product-cover picture`;
    this.categoryDescription = '#category-description';

    // Filter
    this.searchFilters = '#search_filters_wrapper';
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
   * Get number of products displayed in category page
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfProductsDisplayed(page: Page): Promise<number> {
    return (await page.$$(this.productItemListDiv)).length;
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

  // Quick view methods
  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.hover(this.productImg(id));
    let displayed: boolean = false;

    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        (selector) => {
          const element: HTMLElement|null = document.querySelector(selector);

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
      this.waitForVisibleSelector(page, this.quickViewModalDiv),
      page.$eval(this.productQuickViewLink(id), (el: HTMLElement) => el.click()),
    ]);
  }

  /**
   * Is quick view product modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isQuickViewProductModalVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.quickViewModalDiv, 2000);
  }

  /**
   * Returns the URL of the main image in the quickview
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getQuickViewImageMain(page: Page): Promise<string|null> {
    return this.getAttributeContent(page, `${this.quickViewModalProductImageCover} source`, 'srcset');
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
  async getCategoryImageMain(page: Page, name: string): Promise<string|null> {
    return this.getAttributeContent(page, `${this.subCategoriesItem(name)} source`, 'srcset');
  }

  /**
   * Returns the position of a specific product in a list
   * @param page {Page} Browser tab
   * @param idProduct {number} ID of a product
   * @return {Promise<number|null>}
   */
  async getNThChildFromIDProduct(page:Page, idProduct: number): Promise<number|null> {
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

  /**
   * Return if search filters are visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasSearchFilters(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.searchFilters);
  }
}

export default new Category();
