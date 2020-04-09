require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SearchResults extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for search Results page
    this.productArticle = '#js-product-list .products div:nth-child(%NUMBER) article';
    this.productImg = `${this.productArticle} img`;
  }

  /**
   * Go to the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await this.clickAndWaitForNavigation(this.productImg.replace('%NUMBER', id));
  }
};
