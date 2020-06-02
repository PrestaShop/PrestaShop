require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Home extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.homePageSection = 'section#content.page-home';
    this.productArticle = number => `#content .products div:nth-child(${number}) article`;
    this.productImg = number => `${this.productArticle(number)} img`;
    this.productDescriptionDiv = number => `${this.productArticle(number)} div.product-description`;
    this.productQuickViewLink = number => `${this.productArticle(number)} a.quick-view`;
    this.allProductLink = '#content a.all-product-link';
    this.totalProducts = '#js-product-list-top .total-products > p';
    this.productPrice = number => `${this.productArticle(number)} span[aria-label="Price"]`;
    this.newFlag = number => `${this.productArticle(number)} .product-flag.new`;
    this.searchInput = '#search_widget input.ui-autocomplete-input';
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
   * @returns {Promise<boolean>}
   */
  async isHomePage() {
    return this.elementVisible(this.homePageSection, 3000);
  }

  /**
   * Go to the product page
   * @param id, product id
   * @returns {Promise<void>}
   */
  async goToProductPage(id) {
    await this.clickAndWaitForNavigation(this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param id, index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(id) {
    await this.page.hover(this.productImg(id));
    let displayed = false;
    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await this.page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector), ':after')
          .getPropertyValue('display') === 'block',
        this.productDescriptionDiv(id),
      );
      await this.page.waitForTimeout(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.waitForVisibleSelector(this.quickViewModalDiv),
      this.page.$eval(this.productQuickViewLink(id), el => el.click()),
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
    await this.setValue(this.quantityWantedInput, quantity_wanted.toString());
    await Promise.all([
      this.waitForVisibleSelector(this.blockCartModalDiv),
      this.page.click(this.addToCartButton),
    ]);
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @return {Promise<void>}
   */
  async proceedToCheckout() {
    await this.clickAndWaitForNavigation(this.blockCartModalCheckoutLink);
  }

  /**
   * Check product price
   * @param id, index of product in list of products
   * @return {Promise<boolean>}
   */
  async isPriceVisible(id = 1) {
    return this.elementVisible(this.productPrice(id), 1000);
  }

  /**
   * Check new flag
   * @param id
   * @returns {Promise<boolean>}
   */
  async isNewFlagVisible(id = 1) {
    return this.elementVisible(this.newFlag(id), 1000);
  }

  /**
   * Search product
   * @param productName
   * @returns {Promise<void>}
   */
  async searchProduct(productName) {
    await this.setValue(this.searchInput, productName);
    await this.page.keyboard.press('Enter');
    await this.page.waitForNavigation();
  }

  /**
   * Go to home category page by clicking on all products
   * @return {Promise<void>}
   */
  async goToAllProductsPage() {
    await this.clickAndWaitForNavigation(this.allProductLink);
  }
};
