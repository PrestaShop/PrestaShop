require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class SearchResults extends FOBasePage {
  constructor() {
    super();

    // Selectors for search Results page
    this.productArticle = number => `#js-product-list .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
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
}

module.exports = new SearchResults();
