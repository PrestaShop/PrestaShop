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

    // Products list
    this.productList = '#js-product-list';
    this.productArticle = number => `${this.productList} .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = number => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = number => `${this.productArticle(number)} a.quick-view`;

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
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
   * @returns {Promise<object>}
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
    return this.getTextContent(page, this.paginationText, true);
  }

  /**
   * Go to the next page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToNextPage(page) {
    await this.clickAndWaitForNavigation(page, '#js-product-list nav.pagination a[rel=\'next\']');
  }

  // Quick view methods
  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page, id) {
    await page.hover(this.productImg(id));
    let displayed = false;

    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector), ':after')
          .getPropertyValue('display') === 'block',
        this.productDescriptionDiv(id),
      );
      await page.waitForTimeout(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.waitForVisibleSelector(page, this.quickViewModalDiv),
      page.$eval(this.productQuickViewLink(id), el => el.click()),
    ]);
  }

  /**
   * Is quick view product modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuickViewProductModalVisible(page) {
    return this.elementVisible(page, this.quickViewModalDiv, 2000);
  }
}

module.exports = new Category();
