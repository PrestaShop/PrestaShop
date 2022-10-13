require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Home page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Home extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on home page
   */
  constructor() {
    super();

    this.pageTitle = global.INSTALL.SHOP_NAME;

    // Selectors of slider
    this.carouselSliderId = '#carousel';
    this.carouselControlDirectionLink = direction => `${this.carouselSliderId} a.${direction}.carousel-control`;
    this.carouselSliderInnerList = `${this.carouselSliderId} ul.carousel-inner`;
    this.carouselSliderInnerListItems = `${this.carouselSliderInnerList} li`;
    this.carouselSliderInnerListItem = position => `${this.carouselSliderInnerListItems}:nth-child(${position})`;

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
    this.quickViewProductDimension = `${this.quickViewProductVariants} select#group_3`;
    this.productAvailability = '#product-availability';
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
    this.continueShoppingButton = `${this.blockCartModalDiv} div.cart-content-btn button.btn-secondary`;

    // Newsletter subscription messages
    this.successSubscriptionMessage = 'You have successfully subscribed to this newsletter.';
    this.alreadyUsedEmailMessage = 'This email address is already registered.';
  }

  /**
   * Check home page
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isHomePage(page) {
    return this.elementVisible(page, this.homePageSection, 3000);
  }

  /**
   * Click on right/left arrow of the slider
   * @param page {Page} Browser tab
   * @param direction {string} Direction to click on
   * @returns {Promise<void>}
   */
  async clickOnLeftOrRightArrow(page, direction) {
    await page.click(this.carouselControlDirectionLink(direction));
  }

  /**
   * Is slider visible
   * @param page {Page} Browser tab
   * @param position {number} The slider position
   * @returns {Promise<boolean>}
   */
  async isSliderVisible(page, position) {
    await this.waitForVisibleSelector(page, this.carouselSliderId);

    return this.elementVisible(page, this.carouselSliderInnerListItem(position), 1000);
  }

  /**
   * Click on slider number
   * @param page {Page} Browser tab
   * @param position {number} The slider position
   * @returns {Promise<string>}
   */
  async clickOnSlider(page, position) {
    await this.clickAndWaitForNavigation(page, this.carouselSliderInnerListItem(position));

    return this.getCurrentURL(page);
  }

  /**
   * Go to the product page
   * @param page {Page} Browser tab
   * @param id {number} Product id
   * @returns {Promise<void>}
   */
  async goToProductPage(page, id) {
    await this.clickAndWaitForNavigation(page, this.productImg(id));
  }

  /**
   * Check product price
   * @param page {Page} Browser tab
   * @param id {number} index of product in list of products
   * @return {Promise<boolean>}
   */
  async isPriceVisible(page, id = 1) {
    return this.elementVisible(page, this.productPrice(id), 1000);
  }

  /**
   * Check new flag
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @returns {Promise<boolean>}
   */
  async isNewFlagVisible(page, id = 1) {
    return this.elementVisible(page, this.newFlag(id), 1000);
  }

  /**
   * Go to home category page by clicking on all products
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToAllProductsPage(page) {
    await this.clickAndWaitForNavigation(page, this.allProductLink);
  }

  /**
   * Get popular product title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPopularProductTitle(page) {
    return this.getTextContent(page, this.popularProductTitle);
  }

  // Quick view methods
  /**
   * Click on Quick view Product
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
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
   * Is quick view product modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuickViewProductModalVisible(page) {
    return this.elementVisible(page, this.quickViewModalDiv, 2000);
  }

  /**
   * Add product to cart with Quick view
   * @param page {Page} Browser tab
   * @param id {number} Index of product in list of products
   * @param quantity_wanted {number} Quantity to order
   * @return {Promise<void>}
   */
  async addProductToCartByQuickView(page, id, quantity_wanted = 1) {
    await this.quickViewProduct(page, id);
    await this.setValue(page, this.quickViewQuantityWantedInput, quantity_wanted);
    await Promise.all([
      this.waitForVisibleSelector(page, this.blockCartModalDiv),
      page.click(this.addToCartButton),
    ]);
  }

  /**
   * Change product attributes
   * @param page {Page} Browser tab
   * @param attributes {object} The attributes data (size, color, dimension)
   * @returns {Promise<void>}
   */
  async changeCombination(page, attributes) {
    if (attributes.size) {
      await this.selectByVisibleText(page, this.quickViewProductSize, attributes.size);
    }
    if (attributes.color) {
      await this.waitForSelectorAndClick(page, `${this.quickViewProductColor} input[title='${attributes.color}']`);
      await this.waitForVisibleSelector(
        page,
        `${this.quickViewProductColor} input[title='${attributes.color}'][checked]`,
      );
    }
    if (attributes.dimension) {
      await Promise.all([
        page.waitForResponse(response => response.url().includes('product&token=')),
        this.selectByVisibleText(page, this.quickViewProductDimension, attributes.dimension),
      ]);
    }
  }

  /**
   * Change product quantity
   * @param page {Page} Browser tab
   * @param quantity {number} The product quantity to change
   * @returns {Promise<void>}
   */
  async changeQuantity(page, quantity) {
    await this.setValue(page, this.quickViewQuantityWantedInput, quantity);
  }

  /**
   * Click on add to cart button from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async addToCartByQuickView(page) {
    await this.waitForSelectorAndClick(page, this.addToCartButton);
  }

  /**
   * Change combination and add to cart
   * @param page {Page} Browser tab
   * @param combination {object} The combination data (size, color, quantity)
   * @returns {Promise<void>}
   */
  async changeCombinationAndAddToCart(page, combination) {
    await this.changeCombination(page, combination);
    await this.changeQuantity(page, combination.quantity);
    await this.addToCartByQuickView(page);
  }

  /**
   * Get product with discount details from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{discountPercentage: string, thumbImage: string, price: number, taxShippingDeliveryLabel: string,
   * regularPrice: number, coverImage: string, name: string, shortDescription: string}>}
   */
  async getProductWithDiscountDetailsFromQuickViewModal(page) {
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
   * Get product details from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{thumbImage: string, price: number, taxShippingDeliveryLabel: string,
   * coverImage: string, name: string, shortDescription: string}>}
   */
  async getProductDetailsFromQuickViewModal(page) {
    return {
      name: await this.getTextContent(page, this.quickViewProductName),
      price: parseFloat((await this.getTextContent(page, this.quickViewProductPrice)).replace('€', '')),
      taxShippingDeliveryLabel: await this.getTextContent(page, this.quickViewTaxShippingDeliveryLabel),
      shortDescription: await this.getTextContent(page, this.quickViewShortDescription),
      coverImage: await this.getAttributeContent(page, this.quickViewCoverImage, 'src'),
      thumbImage: await this.getAttributeContent(page, this.quickViewThumbImage, 'src'),
    };
  }

  /**
   * Get selected attribute from quick view
   * @param page {Page} Browser tab
   * @param attribute {object} Attribute to get value
   * @returns {Promise<{size: string, color: string}|{dimension: string}>}
   */
  async getSelectedAttributesFromQuickViewModal(page, attribute) {
    let attributes;
    if (attribute.size) {
      attributes = {
        size: await page.getAttribute(`${this.quickViewProductSize} option[selected]`, 'title'),
        color: await page.getAttribute(`${this.quickViewProductColor} input[checked='checked']`, 'title'),
      };
    } else if (attribute.dimension) {
      attributes = {
        dimension: await page.getAttribute(`${this.quickViewProductDimension} option[selected]`, 'title'),
      };
    }
    return attributes;
  }

  /**
   * Get product attributes from quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<{size: string, color: string}>}
   */
  async getProductAttributesFromQuickViewModal(page) {
    return {
      size: await this.getTextContent(page, this.quickViewProductSize),
      color: await this.getTextContent(page, this.quickViewProductColor, false),
    };
  }

  /**
   * Close quick view modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeQuickViewModal(page) {
    await this.waitForSelectorAndClick(page, this.quickViewCloseButton);

    return this.elementNotVisible(page, this.quickViewModalDiv, 1000);
  }

  /**
   * Close block cart modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeBlockCartModal(page) {
    await this.waitForSelectorAndClick(page, this.blockCartModalCloseButton);

    return this.elementNotVisible(page, this.blockCartModalDiv, 1000);
  }

  /**
   * Select product color
   * @param page {Page} Browser tab
   * @param id {number} Id of the current product
   * @param color {string} The color to select
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

    await this.clickAndWaitForNavigation(page, this.productColorLink(id, color));
  }

  /**
   * Get product availability text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductAvailabilityText(page) {
    return this.getTextContent(page, this.productAvailability);
  }

  /**
   * Is add to cart button enabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddToCartButtonEnabled(page) {
    return !await this.elementVisible(page, `${this.addToCartButton}[disabled]`, 1000);
  }

  // Block cart modal methods
  /**
   * Is block cart modal visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isBlockCartModalVisible(page) {
    return this.elementVisible(page, this.blockCartModalDiv, 2000);
  }

  /**
   * Get product details from blockCart modal
   * @param page {Page} Browser tab
   * @returns {Promise<{quantity: number, price: number, name: string, cartShipping: string, cartSubtotal: number,
   * totalTaxIncl: number, cartProductsCount: number}>}
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
   * @param page {Page} Browser tab
   * @returns {Promise<{size: string, color: string}>}
   */
  async getProductAttributesFromBlockCartModal(page) {
    return {
      size: await this.getTextContent(page, this.cartModalProductSizeBlock),
      color: await this.getTextContent(page, this.cartModalProductColorBlock),
    };
  }

  /**
   * Click on proceed to checkout after adding product to cart (in modal homePage)
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async proceedToCheckout(page) {
    await this.clickAndWaitForNavigation(page, this.cartModalCheckoutLink);
    await page.waitForLoadState('domcontentloaded');
  }

  /**
   * Click on continue shopping
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async continueShopping(page) {
    await this.waitForSelectorAndClick(page, this.continueShoppingButton);
    return this.elementNotVisible(page, this.blockCartModalDiv, 2000);
  }

  /**
   * Go to social sharing link
   * @param page {Page} Browser tab
   * @param socialSharing {string} The social network name
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
   * @param page {Page} Browser tab
   * @param email {string} Email to set on input
   * @returns {Promise<string>}
   */
  async subscribeToNewsletter(page, email) {
    await this.setValue(page, this.newsletterFormField, email);
    await this.waitForSelectorAndClick(page, this.newsletterSubmitButton);

    return this.getTextContent(page, this.subscriptionAlertMessage);
  }
}

module.exports = new Home();
