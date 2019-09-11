const CommonPage = require('../commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.logoHomePage = '#_desktop_logo';
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
    this.productArticle = '#content .products div:nth-child(%NUMBER) article';
    this.productImg = `${this.productArticle} img`;
    this.productQuickViewLink = `${this.productArticle} a.quick-view`;
    this.userInfoLink = '#_desktop_user_info';
    this.logoutLink = `${this.userInfoLink} .user-info a.logout`;
    this.contactLink = '#contact-link';
    this.allProductLink = '#content a.all-product-link';
    this.totalProducts = '#js-product-list-top .total-products > p';
    this.categoryMenu = '#category-%ID > a';
    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quantityWantedInput = `${this.quickViewModalDiv} input#quantity_wanted`;
    this.addToCartButton = `${this.quickViewModalDiv} button[data-button-action='add-to-cart']`;
    // Block Cart Modal
    this.blockCartModalDiv = '#blockcart-modal';
    this.blockCartModalCheckoutLink = `${this.blockCartModalDiv} div.cart-content-btn a`;
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

  /**
   * Go to login Page
   * @return {Promise<void>}
   */
  async goToLoginPage() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.userInfoLink),
    ]);
  }

  /**
   * Check if customer is connected
   * @return {Promise<boolean|true>}
   */
  async isCustomerConnected() {
    return this.elementVisible(this.logoutLink, 1000);
  }

  /**
   * Click on Quick view Product
   * @param id, index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(id) {
    await Promise.all([
      this.page.waitForSelector(`${this.productQuickViewLink.replace('%NUMBER', id)}`),
      this.page.hover(this.productImg.replace('%NUMBER', id)),
    ]);
    await Promise.all([
      this.page.waitForSelector(this.quickViewModalDiv),
      this.page.$eval(this.productQuickViewLink.replace('%NUMBER', id), el => el.click()),
    ]);
  }

  /**
   * Add product to cart with Quick view 
   * @param id, index of product in list of products
   * @param quantity_wanted, quantity to order
   * @return {Promise<void>}
   */
  async addProductToCartByQuickView(id, quantity_wanted = '1') {
    await this.quickViewProduct(id);
    await this.setValue(this.quantityWantedInput, quantity_wanted);
    await Promise.all([
      this.page.waitForSelector(this.blockCartModalDiv),
      this.page.click(this.addToCartButton),
    ]);
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @return {Promise<void>}
   */
  async proceedToCheckout() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.blockCartModalCheckoutLink),
    ]);
  }
};
