import FOBasePage from '@pages/FO/FObasePage';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';

import type {Page} from 'playwright';

/**
 * Search page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SearchResultsPage extends FOBasePage {
  public readonly pageTitle: string;

  private readonly productListTopDiv: string;

  private readonly totalProduct: string;

  protected productArticle: (number: number) => string;

  protected productImg: (number: number) => string;

  private readonly productDescriptionDiv: (number: number) => string;

  private readonly productAttribute: (number: number, attribute: string) => string;

  protected productQuickViewLink: (number: number) => string;

  protected productPrice: string;

  protected productNoMatches: string;

  private readonly sortButton: string;

  private readonly sortDropDownMenu: string;

  private readonly sortOption: (sortBy: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on search page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Search';

    // Selectors for search Results page
    this.productListTopDiv = '#js-product-list-top';
    this.totalProduct = `${this.productListTopDiv} .total-products`;
    this.productArticle = (number: number) => `#js-product-list .products div:nth-child(${number}) article`;
    this.productAttribute = (number: number, attribute: string) => `${this.productArticle(number)} .product-${attribute}`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = (number: number) => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} a.quick-view`;
    this.productPrice = '#js-product-list div.product-description span.price';
    this.productNoMatches = '#product-search-no-matches';
    // Selectors for sort button
    this.sortButton = '#js-product-list-top  button.select-title';
    this.sortDropDownMenu = '.dropdown-menu.dropdown-menu-start.show';
    this.sortOption = (sortBy: string) => `#js-product-list-top a[href*='${sortBy}']`;
  }

  // Methods
  /**
   * Check if there are results
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasResults(page: Page): Promise<boolean> {
    return (await page.locator(this.productNoMatches).count()) === 0;
  }

  /**
   * Get search product results number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getSearchResultsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.totalProduct);
  }

  /**
   * Get sort by value from button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getSortByValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.sortButton);
  }

  /**
   * Sort products list
   * @param page {Page} Browser tab
   * @param sortBy {string} Value to sort by
   * @return {Promise<void>}
   */
  async sortProductsList(page: Page, sortBy: string): Promise<void> {
    await page.locator(this.sortButton).click();
    await this.waitForVisibleSelector(page, this.sortDropDownMenu);
    await this.clickAndWaitForURL(page, this.sortOption(sortBy));
  }

  /**
   * Get all products attribute
   * @param page {Page} Browser tab
   * @param attribute {string} Attribute to get
   * @returns {Promise<string[]>}
   */
  async getAllProductsAttribute(page: Page, attribute: string): Promise<string[]> {
    let rowContent: string;
    const rowsNumber: number = await this.getSearchResultsNumber(page);
    const allRowsContentTable: string[] = [];

    for (let i = 1; i <= rowsNumber; i++) {
      rowContent = await this.getTextContent(page, this.productAttribute(i, attribute));
      allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }

  /**
   * Go to the product page
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
   * @returns {Promise<void>}
   */
  async goToProductPage(page: Page, id: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
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

          if (!element) {
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
   * Get the product price value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductPrice(page: Page): Promise<string> {
    return this.getTextContent(page, this.productPrice);
  }
}

const searchResultsPage = new SearchResultsPage();
export {searchResultsPage, SearchResultsPage};
