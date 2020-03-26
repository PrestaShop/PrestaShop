require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Category extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.bodySelector = 'body#category';
    this.mainSection = 'section#main';
    this.productsSection = 'section#products';
    this.productListDiv = 'div#js-product-list';
    this.productItemListDiv = `${this.productListDiv} .products div[itemprop='itemListElement']`;
  }

  /* Methods */
  /**
   *
   * @return {Promise<boolean>}
   */
  async isCategoryPage() {
    return this.elementVisible(this.bodySelector, 2000);
  }

  /**
   *
   * @return {Promise<int>}
   */
  async getNumberOfProductsDisplayed() {
    return (await this.page.$$(this.productItemListDiv)).length;
  }
};
