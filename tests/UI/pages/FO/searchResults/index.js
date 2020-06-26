require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SearchResults extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for search Results page
    this.productArticle = number => `#js-product-list .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
  }

  /**
   * Go to the product page
   * @param id, product id
   * @returns {Promise<void>}
   */
  async goToProductPage(id) {
    await this.clickAndWaitForNavigation(this.productImg(id));
  }
};
