require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class SearchResults extends FOBasePage {
  constructor() {
    super();
    this.pageTitle = 'Search';

    // Selectors for search Results page
    this.productArticle = number => `#js-product-list .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = number => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = number => `${this.productArticle(number)} a.quick-view`;

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewCoverImage = `${this.quickViewModalDiv} img.js-qv-product-cover`;
    this.quickViewThumbFirstImage = `${this.quickViewModalDiv} li:nth-child(1) img.js-thumb`;
    this.quickViewThumb2ndImage = `${this.quickViewModalDiv} li:nth-child(2) img.js-thumb`;
  }

  /**
   * Go to the product page
   * @param page
   * @param id, product id
   * @returns {Promise<void>}
   */
  async goToProductPage(page, id) {
    await this.clickAndWaitForNavigation(page, this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param page
   * @param id, index of product in list of products
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
   * @param page
   * @param id
   * @returns {Promise<string>}
   */
  async selectThumbImage(page, id) {
    if (id === 1) {
      await this.waitForSelectorAndClick(page, this.quickViewThumbFirstImage);
      await page.waitForSelector(`${this.quickViewThumbFirstImage}.selected`, {state: 'visible'});
    } else {
      await this.waitForSelectorAndClick(page, this.quickViewThumb2ndImage);
      await page.waitForSelector(`${this.quickViewThumb2ndImage}.selected`, {state: 'visible'});
    }
    return this.getAttributeContent(page, this.quickViewCoverImage, 'src');
  }
}

module.exports = new SearchResults();
