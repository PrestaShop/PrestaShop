require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Search page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SearchResults extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on search page
   */
  constructor() {
    super();
    this.pageTitle = 'Search';

    // Selectors for search Results page
    this.productListTopDiv = '#js-product-list-top';
    this.totalProduct = `${this.productListTopDiv} .total-products`;
    this.productArticle = number => `#js-product-list .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = number => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = number => `${this.productArticle(number)} a.quick-view`;

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewCoverImage = `${this.quickViewModalDiv} img.js-qv-product-cover`;
    this.quickViewThumbImage = position => `${this.quickViewModalDiv} li:nth-child(${position}) img.js-thumb`;
  }

  // Methods
  /**
   * Get search product results number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getSearchResultsNumber(page) {
    return this.getNumberFromText(page, this.totalProduct);
  }

  /**
   * Go to the product page
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
   * @returns {Promise<void>}
   */
  async goToProductPage(page, id) {
    await this.clickAndWaitForNavigation(page, this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product on the list
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
   * Select thumb image
   * @param page {Page} Browser tab
   * @param position {number} Position of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page, position) {
    await page.click(this.quickViewThumbImage(position));
    await this.waitForVisibleSelector(page, `${this.quickViewThumbImage(position)}.selected`);

    return this.getAttributeContent(page, this.quickViewCoverImage, 'src');
  }
}

module.exports = new SearchResults();
