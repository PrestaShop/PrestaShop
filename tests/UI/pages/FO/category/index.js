require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Category extends FOBasePage {
  constructor() {
    super();

    // Selectors
    this.bodySelector = '#category';
    this.mainSection = '#main';
    this.productsSection = '#products';
    this.productListDiv = '#js-product-list';
    this.productItemListDiv = `${this.productListDiv} .products div.product`;
    this.sortByDiv = `${this.productsSection} div.sort-by-row`;
    this.sortByButton = `${this.sortByDiv} button.select-title`;
  }

  /* Methods */
  /**
   * Check if user is in category page
   * @param page
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
   * Get sort by value from button
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getSortByValue(page) {
    return this.getTextContent(page, this.sortByButton);
  }
}

module.exports = new Category();
