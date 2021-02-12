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
    this.productColorLink = (number, color) => `${this.productArticle(number)} .variant-links a[aria-label='${color}']`;
    this.allProductLink = '#content a.all-product-link';
    this.totalProducts = '#js-product-list-top .total-products > p';
    this.productPrice = number => `${this.productArticle(number)} span[aria-label="Price"]`;
    this.newFlag = number => `${this.productArticle(number)} .product-flag.new`;
    this.searchInput = '#search_widget input.ui-autocomplete-input';

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewCloseButton = `${this.quickViewModalDiv} button.close`;
    this.quickViewProductName = `${this.quickViewModalDiv} h1`;
    this.quickViewRegularPrice = `${this.quickViewModalDiv} span.regular-price`;
    this.quickViewProductPrice = `${this.quickViewModalDiv} div.current-price span[itemprop="price"]`;
    this.quickViewDiscountPercentage = `${this.quickViewModalDiv} div.current-price span.discount-percentage`;
    this.quickViewTaxShippingDeliveryLabel = `${this.quickViewModalDiv} div.tax-shipping-delivery-label`;
    this.quickViewShortDescription = `${this.quickViewModalDiv} div#product-description-short`;
    this.quickViewProductVariants = `${this.quickViewModalDiv} div.product-variants`;
    this.quickViewProductSize = `${this.quickViewProductVariants} select#group_1`;
    this.quickViewProductColor = `${this.quickViewProductVariants} ul#group_2`;
    this.quickViewCoverImage = `${this.quickViewModalDiv} img.js-qv-product-cover`;
    this.quickViewThumbImage = `${this.quickViewModalDiv} img.js-thumb.selected`;
    this.quickViewQuantityWantedInput = `${this.quickViewModalDiv} input#quantity_wanted`;
    this.quickViewFacebookSocialSharing = `${this.quickViewModalDiv} .facebook a`;
    this.quickViewTwitterSocialSharing = `${this.quickViewModalDiv} .twitter a`;
    this.quickViewPinterestSocialSharing = `${this.quickViewModalDiv} .pinterest a`;
    this.addToCartButton = `${this.quickViewModalDiv} button[data-button-action='add-to-cart']`;

    // Block Cart Modal
    this.blockCartModalDiv = '#blockcart-modal';
    this.productName = `${this.blockCartModalDiv} .product-name`;
    this.productPrice = `${this.blockCartModalDiv} .product-price`;
    this.productSize = `${this.blockCartModalDiv} .size strong`;
    this.productColor = `${this.blockCartModalDiv} .color strong`;
    this.productQuantity = `${this.blockCartModalDiv} .product-quantity`;
    this.cartContent = `${this.blockCartModalDiv} .cart-content`;
    this.cartProductsCount = `${this.cartContent} .cart-products-count`;
    this.cartShipping = `${this.cartContent} #shipping`;
    this.cartSubtotal = `${this.cartContent} #subtotals`;
    this.productTaxIncl = `${this.cartContent} .product-total .value`;
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

  // Quick view methods
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
  async addProductToCartByQuickView(page, id, quantity_wanted = 1) {
    await this.quickViewProduct(page, id);
    await this.setValue(page, this.quickViewQuantityWantedInput, quantity_wanted.toString());
    await Promise.all([
      this.waitForVisibleSelector(page, this.blockCartModalDiv),
      page.click(this.addToCartButton),
    ]);
  }

  /**
   * Change combination and add to cart
   * @param page
   * @param combination
   * @returns {Promise<void>}
   */
  async changeCombinationAndAddToCart(page, combination) {
    await this.selectByVisibleText(page, this.quickViewProductSize, combination.size);
    await this.waitForSelectorAndClick(page, `${this.quickViewProductColor} input[title='${combination.color}']`);
    await this.setValue(page, this.quickViewQuantityWantedInput, combination.quantity);
    await this.waitForSelectorAndClick(page, this.addToCartButton);
  }

  /**
   * Get product details from quick view modal
   * @param page
   * @returns {Promise<{discountPercentage: *, thumbImage: *, size: *, color: *, price: *, taxShippingDeliveryLabel: *,
   * regularPrice: *, coverImage: *, name: *, shortDescription: *}>}
   */
  async getProductDetailsFromQuickViewModal(page) {
    return {
      name: await this.getTextContent(page, this.quickViewProductName),
      regularPrice: parseFloat((await this.getTextContent(page, this.quickViewRegularPrice)).replace('€', '')),
      price: parseFloat((await this.getTextContent(page, this.quickViewProductPrice)).replace('€', '')),
      discountPercentage: await this.getTextContent(page, this.quickViewDiscountPercentage),
      taxShippingDeliveryLabel: await this.getTextContent(page, this.quickViewTaxShippingDeliveryLabel),
      shortDescription: await this.getTextContent(page, this.quickViewShortDescription),
      size: await this.getTextContent(page, this.quickViewProductSize),
      color: await this.getTextContent(page, this.quickViewProductColor, false),
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'src'),
    };
  }

  /**
   * Close quick view modal
   * @param page
   * @returns {Promise<boolean>}
   */
  async closeQuickViewModal(page) {
    await this.waitForSelectorAndClick(page, this.quickViewCloseButton);

    return this.elementNotVisible(page, this.quickViewModalDiv, 1000);
  }

  /**
   * Select product color
   * @param page
   * @param id
   * @param color
   * @returns {Promise<void>}
   */
  async selectProductColor(page, id, color) {
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

    await this.waitForSelectorAndClick(page, this.productColorLink(id, color));
  }

  // Block cart modal methods
  /**
   * Get product details from blockCart modal
   * @param page
   * @returns {Promise<{quantity: number, size: *, color: *, price: *, name: *, cartShipping: *, cartSubtotal: *,
   * totalTaxIncl: *, cartProductsCount: number}>}
   */
  async getProductDetailsFromBlockCartModal(page) {
    return {
      name: await this.getTextContent(page, this.productName),
      price: await this.getTextContent(page, this.productPrice),
      size: await this.getTextContent(page, this.productSize),
      color: await this.getTextContent(page, this.productColor),
      quantity: await this.getNumberFromText(page, this.productQuantity),
      cartProductsCount: await this.getNumberFromText(page, this.cartProductsCount),
      cartSubtotal: await this.getTextContent(page, this.cartSubtotal),
      cartShipping: await this.getTextContent(page, this.cartShipping),
      totalTaxIncl: await this.getTextContent(page, this.productTaxIncl),
    };
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @param page
   * @return {Promise<void>}
   */
  async proceedToCheckout(page) {
    await this.clickAndWaitForNavigation(page, this.blockCartModalCheckoutLink);
    await page.waitForLoadState('domcontentloaded');
  }

  /**
   * Go to social sharing link
   * @param page
   * @param socialSharing
   * @returns {Promise<void>}
   */
  async goToSocialSharingLink(page, socialSharing) {
    let selector;
    switch (socialSharing) {
      case 'Facebook':
        selector = this.quickViewFacebookSocialSharing;
        break;

      case 'Twitter':
        selector = this.quickViewTwitterSocialSharing;
        break;

      case 'Pinterest':
        selector = this.quickViewPinterestSocialSharing;
        break;

      default:
        throw new Error(`${socialSharing} was not found`);
    }

    return this.openLinkWithTargetBlank(page, selector, 'body');
  }
}

module.exports = new Home();
