require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class SearchProduct extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for search product page
    this.searchResultSection = 'section#wrapper #js-product-list';
    this.searchResultProductArticle = `${this.searchResultSection} div:nth-child(%NUMBER) > article`;
    this.productImg = `${this.searchResultProductArticle} img`;
  }

  /**
   * Go to the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await this.waitForSelectorAndClick(this.productImg.replace('%NUMBER', id));
  }
};
