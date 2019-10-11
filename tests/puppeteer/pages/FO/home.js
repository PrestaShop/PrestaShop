const FOBasePage = require('../FO/FObasePage');

module.exports = class Home extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.homePageSection = 'section#content.page-home';
    this.productArticle = '#content .products div:nth-child(%NUMBER) article';
    this.productImg = `${this.productArticle} img`;
    this.productQuickViewLink = `${this.productArticle} a.quick-view`;
    this.allProductLink = '#content a.all-product-link';
    this.totalProducts = '#js-product-list-top .total-products > p';
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
    await this.page.waitForSelector(this.homePageSection);
  }

  /**
   * Go to the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await this.waitForSelectorAndClick(this.productImg.replace('%NUMBER', id), 5000);
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
