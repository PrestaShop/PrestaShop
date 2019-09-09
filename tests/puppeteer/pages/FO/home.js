const CommonPage = require('../commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.logoHomePage = '#_desktop_logo';
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
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
   * go to the home page
   */
  async goToHomePage() {
    await this.waitForSelectorAndClick(this.logoHomePage);
    this.page.waitForNavigation({waitUntil: 'networkidle0'});
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
