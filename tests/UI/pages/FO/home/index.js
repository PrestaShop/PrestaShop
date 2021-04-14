require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Home extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = global.INSTALL.SHOP_NAME;

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
    this.newsletterFormField = '.block_newsletter [name=email]';
    this.newsletterSubmitButton = '.block_newsletter [name=submitNewsletter]';

    // Newsletter Subscription alert message
    this.subscriptionAlertMessage = '.block_newsletter_alert';

    // Quick View modal
    this.quickViewModalDiv = 'div[id*=\'quickview-modal\']';
    this.quickViewCloseButton = `${this.quickViewModalDiv} button.close`;
    this.quickViewProductName = `${this.quickViewModalDiv} h1`;
    this.quickViewRegularPrice = `${this.quickViewModalDiv} span.regular-price`;
    this.quickViewProductPrice = `${this.quickViewModalDiv} div.current-price span.current-price-value`;
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
    this.blockCartModalCloseButton = `${this.blockCartModalDiv} button.close`;
    this.cartModalProductNameBlock = `${this.blockCartModalDiv} .product-name`;
    this.cartModalProductPriceBlock = `${this.blockCartModalDiv} .product-price`;
    this.cartModalProductSizeBlock = `${this.blockCartModalDiv} .size strong`;
    this.cartModalProductColorBlock = `${this.blockCartModalDiv} .color strong`;
    this.cartModalProductQuantityBlock = `${this.blockCartModalDiv} .product-quantity`;
    this.cartContentBlock = `${this.blockCartModalDiv} .cart-content`;
    this.cartModalProductsCountBlock = `${this.cartContentBlock} .cart-products-count`;
    this.cartModalShippingBlock = `${this.cartContentBlock} .shipping.value`;
    this.cartModalSubtotalBlock = `${this.cartContentBlock} .subtotal.value`;
    this.cartModalproductTaxInclBlock = `${this.cartContentBlock} .product-total .value`;
    this.cartModalCheckoutLink = `${this.blockCartModalDiv} div.cart-content-btn a`;

    // Newsletter subscription messages
    this.successSubscriptionMessage = 'You have successfully subscribed to this newsletter.';
    this.alreadyUsedEmailMessage = 'This email address is already registered.';
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
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'src'),
    };
  }

  /**
   * Get product attributes from quick view modal
   * @param page
   * @returns {Promise<{size: *, color: *}>}
   */
  async getProductAttributesFromQuickViewModal(page) {
    return {
      size: await this.getTextContent(page, this.quickViewProductSize),
      color: await this.getTextContent(page, this.quickViewProductColor, false),
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
   * Close block cart modal
   * @param page
   * @returns {Promise<boolean>}
   */
  async closeBlockCartModal(page) {
    await this.waitForSelectorAndClick(page, this.blockCartModalCloseButton);

    return this.elementNotVisible(page, this.blockCartModalDiv, 1000);
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
      name: await this.getTextContent(page, this.cartModalProductNameBlock),
      price: parseFloat((await this.getTextContent(page, this.cartModalProductPriceBlock)).replace('€', '')),
      quantity: await this.getNumberFromText(page, this.cartModalProductQuantityBlock),
      cartProductsCount: await this.getNumberFromText(page, this.cartModalProductsCountBlock),
      cartSubtotal: parseFloat((await this.getTextContent(page, this.cartModalSubtotalBlock)).replace('€', '')),
      cartShipping: await this.getTextContent(page, this.cartModalShippingBlock),
      totalTaxIncl: parseFloat((await this.getTextContent(page, this.cartModalproductTaxInclBlock)).replace('€', '')),
    };
  }

  /**
   * Get product attributes from block cart modal
   * @param page
   * @returns {Promise<{size: *, color: *}>}
   */
  async getProductAttributesFromBlockCartModal(page) {
    return {
      size: await this.getTextContent(page, this.cartModalProductSizeBlock),
      color: await this.getTextContent(page, this.cartModalProductColorBlock),
    };
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @param page
   * @return {Promise<void>}
   */
  async proceedToCheckout(page) {
    await this.clickAndWaitForNavigation(page, this.cartModalCheckoutLink);
    await page.waitForLoadState('domcontentloaded');
  }

  /**
   * Go to social sharing link
   * @param page
   * @param socialSharing
   * @returns {Promise<void>}
   */
  async getSocialSharingLink(page, socialSharing) {
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

    return this.getAttributeContent(page, selector, 'href');
  }

  /**
   * Subscribe to the newsletter from the FO homepage
   * @param {object} page
   * @param {string} email
   *
   * @returns {Promise<string|TextContent|*>}
   */
  async subscribeToNewsletter(page, email) {
    await this.setValue(page, this.newsletterFormField, email);
    await this.waitForSelectorAndClick(page, this.newsletterSubmitButton);

    return this.getTextContent(page, this.subscriptionAlertMessage);
  }
}

module.exports = new Home();
