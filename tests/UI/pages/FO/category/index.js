require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Category page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Category extends FOBasePage {
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
    this.paginationText = `${this.productListDiv} .pagination div:nth-child(1)`;
    this.sortByDiv = `${this.productsSection} div.sort-by-row`;
    this.sortByButton = `${this.sortByDiv} button.select-title`;
  }

  /* Methods */
  /**
   * Check if user is in category page
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCategoryPage(page) {
    return this.elementVisible(page, this.bodySelector, 2000);
  }

  /**
   * Get number of products displayed in category page
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getNumberOfProductsDisplayed(page) {
    return (await page.$$(this.productItemListDiv)).length;
  }

  /**
  * Get number of all products
  * @param page {Page}
  * @returns {Promise<number>}
  */
  async getNumberOfProducts(page) {
    return this.getNumberFromText(page, this.productListTop);
  }

  /**
  * Get the header name of the page
  * @param page {Page}
  * @returns {Promise<string>}
  */
  async getHeaderPageName(page) {
    return page.locator(this.headerNamePage).innerText().valueOf();
  }

  /**
   * Get sort by value from button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getSortByValue(page) {
    return this.getTextContent(page, this.sortByButton);
  }

  /**
   * Is Sort By Button Visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isSortButtonVisible(page) {
    return this.elementVisible(page, this.sortByButton, 1000);
  }

  /**
   * Get showing Items
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getShowingItems(page) {
    return this.getTextContent(page, this.paginationText, 1000);
  }
}

module.exports = new Category();
