require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Home extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.homePageSection = 'section#content.page-home';
    this.productArticle = '#content .products div:nth-child(%NUMBER) article';
    this.productImg = `${this.productArticle} img`;
    this.productDescriptionDiv = `${this.productArticle} div.product-description`;
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
  async isHomePage() {
    return this.elementVisible(this.homePageSection, 3000);
  }

  /**
   * Go to the product page
   * @param id, product id
   */
  async goToProductPage(id) {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.productImg.replace('%NUMBER', id)),
    ]);
  }

  /**
   * Click on Quick view Product
   * @param id, index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(id) {
    await this.page.hover(this.productImg.replace('%NUMBER', id));
    let displayed = false;
    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      displayed = await this.page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector), ':after')
          .getPropertyValue('display') === 'block',
        this.productDescriptionDiv.replace('%NUMBER', id),
      );
      await this.page.waitFor(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.page.waitForSelector(this.quickViewModalDiv, {visible: true}),
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
      this.page.waitForSelector(this.blockCartModalDiv, {visible: true}),
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
