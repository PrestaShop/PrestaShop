require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Home extends FOBasePage {
  constructor() {
    super();

    // Selectors for home page
    this.homePageSection = 'section#content.page-home';
    this.popularProductTitle = '#content section h2';
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
   * @param page
   * @returns {Promise<boolean>}
   */
  async isHomePage(page) {
    return this.elementVisible(page, this.homePageSection, 3000);
  }

  /**
   * Go to the product page
   * @param page
   * @param id, product id
   * @returns {Promise<void>}
   */
  async goToProductPage(page, id) {
    await this.clickAndWaitForNavigation(page, this.productImg(id));
  }

  /**
   * Click on Quick view Product
   * @param page
   * @param id, index of product in list of products
   * @return {Promise<void>}
   */
  async quickViewProduct(page, id) {
    await page.hover(this.productImg(id));
    let displayed = false;
    /* eslint-disable no-await-in-loop */
    // Only way to detect if element is displayed is to get value of computed style 'product description' after hover
    // and compare it with value 'block'
    for (let i = 0; i < 10 && !displayed; i++) {
      /* eslint-env browser */
      displayed = await page.evaluate(
        selector => window.getComputedStyle(document.querySelector(selector), ':after')
          .getPropertyValue('display') === 'block',
        this.productDescriptionDiv(id),
      );
      await page.waitForTimeout(100);
    }
    /* eslint-enable no-await-in-loop */
    await Promise.all([
      this.waitForVisibleSelector(page, this.quickViewModalDiv),
      page.$eval(this.productQuickViewLink(id), el => el.click()),
    ]);
  }

  /**
   * Add product to cart with Quick view
   * @param page
   * @param id, index of product in list of products
   * @param quantity_wanted, quantity to order
   * @return {Promise<void>}
   */
  async addProductToCartByQuickView(page, id, quantity_wanted = '1') {
    await this.quickViewProduct(page, id);
    await this.setValue(page, this.quantityWantedInput, quantity_wanted.toString());
    await Promise.all([
      this.waitForVisibleSelector(page, this.blockCartModalDiv),
      page.click(this.addToCartButton),
    ]);
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @param page
   * @return {Promise<void>}
   */
  async proceedToCheckout(page) {
    await this.clickAndWaitForNavigation(page, this.blockCartModalCheckoutLink);
  }

  /**
   * Check product price
   * @param page
   * @param id, index of product in list of products
   * @return {Promise<boolean>}
   */
  async isPriceVisible(page, id = 1) {
    return this.elementVisible(page, this.productPrice(id), 1000);
  }

  /**
   * Check new flag
   * @param page
   * @param id
   * @returns {Promise<boolean>}
   */
  async isNewFlagVisible(page, id = 1) {
    return this.elementVisible(page, this.newFlag(id), 1000);
  }

  /**
   * Search product
   * @param page
   * @param productName
   * @returns {Promise<void>}
   */
  async searchProduct(page, productName) {
    await this.setValue(page, this.searchInput, productName);
    await page.keyboard.press('Enter');
    await page.waitForNavigation();
  }

  /**
   * Go to home category page by clicking on all products
   * @param page
   * @return {Promise<void>}
   */
  async goToAllProductsPage(page) {
    await this.clickAndWaitForNavigation(page, this.allProductLink);
  }

  /**
   * Get popular product title
   * @param page
   * @returns {Promise<string>}
   */
  getPopularProductTitle(page) {
    return this.getTextContent(page, this.popularProductTitle);
  }
}

module.exports = new Home();
