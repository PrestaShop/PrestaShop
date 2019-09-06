const CommonPage = require('../commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.logoHomePage = '#_desktop_logo';
    this.productImg = '#content .products div:nth-child(%NUMBER) article img';
    this.userInfoLink = '#_desktop_user_info';
    this.contactLink = '#contact-link';
    this.allProductLink = '#content a.all-product-link';
    this.totalProducts = '#js-product-list-top .total-products > p';
    this.categoryMenu = '#category-%ID > a';
  }

  /**
   * Check home page
   */
  async checkHomePage() {
    await this.page.waitForSelector(this.logoHomePage, {visible: true});
  }

  /**
   * Go to the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await this.page.waitForSelector(this.logoHomePage, {visible: true});
    await this.waitForSelectorAndClick(this.productImg.replace('%NUMBER', id), 5000);
  }

  /**
   * Get number of products displayed in all products page
   * @return integer
   */
  async getNumberOfProducts() {
    const productNumber = await this.getTextContent(this.totalProducts);
    const numberOfProduct = /\d+/g.exec(productNumber).toString();
    return parseInt(numberOfProduct, 10);
  }

  /**
   * Filter by category
   * @param categoryID, category id from the BO
   */
  async filterByCategory(categoryID) {
    await this.waitForSelectorAndClick(this.categoryMenu.replace('%ID', categoryID));
  }

  /**
   * Filter by subcategory
   * @param categoryID, category id from the BO
   * @param subCategoryID, subcategory id from the BO
   */
  async filterSubCategory(categoryID, subCategoryID) {
    await this.page.hover(this.categoryMenu.replace('%ID', categoryID));
    await this.waitForSelectorAndClick(this.categoryMenu.replace('%ID', subCategoryID));
  }
};
