require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Category extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.bodySelector = '#category';
    this.mainSection = '#main';
    this.productsSection = '#products';
    this.productListDiv = '#js-product-list';
    this.productItemListDiv = `${this.productListDiv} .products div[itemprop='itemListElement']`;
  }

  /* Methods */
  /**
   * Check if user is in category page
   * @return {Promise<boolean>}
   */
  async isCategoryPage() {
    return this.elementVisible(this.bodySelector, 2000);
  }

  /**
   * Get number of products displayed in category page
   * @return {Promise<int>}
   */
  async getNumberOfProductsDisplayed() {
    return (await this.page.$$(this.productItemListDiv)).length;
  }
};
