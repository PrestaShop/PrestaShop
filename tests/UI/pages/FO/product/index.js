require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Product extends FOBasePage {
  constructor() {
    super();

    // Selectors for product page
    this.productName = '#main h1[itemprop="name"]';
    this.productPrice = '#main span[itemprop="price"]';
    this.productQuantity = '#quantity_wanted';
    this.productDescription = '#description';
    this.colorInput = color => `#group_2 li input[title=${color}]`;
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.blockCartModal = '#blockcart-modal';
    this.proceedToCheckoutButton = `${this.blockCartModal} div.cart-content-btn a`;
    this.productQuantitySpan = '#product-details div.product-quantities label';
    this.productDetail = 'div.product-information a[href=\'#product-details\']';
    this.continueShoppingButton = `${this.blockCartModal} div.cart-content-btn button`;
    this.productAvailabilityIcon = '#product-availability i';
    this.productAvailability = '#product-availability';
    this.productSizeOption = size => `#group_1 option[title=${size}]`;
    this.productColorInput = color => `#group_2 input[title=${color}]`;
    this.metaLink = '#main > meta';
    // Product prices block
    this.productPricesBlock = 'div.product-prices';
    this.taxShippingDeliveryBlock = `${this.productPricesBlock} div.tax-shipping-delivery-label`;
    this.deliveryInformationSpan = `${this.taxShippingDeliveryBlock} span.delivery-information`;
    this.discountTable = '.table-product-discounts';
    this.discountColumn = `${this.discountTable} th:nth-child(2)`;
    this.discountValue = `${this.discountTable} td:nth-child(2)`;
  }

  /**
   * Get Product information (Product name, price, description)
   * @param page
   * @returns {Promise<{price: (number), name: (string), description: (string)}>}
   */
  async getProductInformation(page) {
    return {
      name: await this.getTextContent(page, this.productName),
      price: parseFloat(await this.getAttributeContent(page, this.productPrice, 'content')),
      description: await this.getTextContent(page, this.productDescription),
    };
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   * @param page
   * @param quantity
   * @param attributeToChoose
   * @param proceedToCheckout
   * @returns {Promise<void>}
   */
  async addProductToTheCart(page, quantity = 1, attributeToChoose = '', proceedToCheckout = true) {
    await page.waitForTimeout(1000);
    if (attributeToChoose.color) {
      await Promise.all([
        this.waitForVisibleSelector(page, this.colorInput(attributeToChoose.color)),
        page.click(this.colorInput(attributeToChoose.color)),
      ]);
    }
    if (quantity !== 1) {
      await this.setValue(page, this.productQuantity, attributeToChoose.quantity.toString());
    }
    await this.waitForSelectorAndClick(page, this.addToCartButton);
    await this.waitForVisibleSelector(page, `${this.blockCartModal}[style*='display: block;']`);
    if (proceedToCheckout) {
      await this.waitForVisibleSelector(page, this.proceedToCheckoutButton);
      await this.clickAndWaitForNavigation(page, this.proceedToCheckoutButton);
    } else {
      await this.waitForSelectorAndClick(page, this.continueShoppingButton);
      await page.waitForSelector(this.continueShoppingButton, {hidden: true});
    }
  }

  /**
   * Is quantity displayed
   * @param page
   * @returns {Promise<boolean>}
   */
  async isQuantityDisplayed(page) {
    await this.waitForSelectorAndClick(page, this.productDetail);
    return this.elementVisible(page, this.productQuantitySpan, 1000);
  }

  /**
   * Is availability product displayed
   * @param page
   * @returns {boolean}
   */
  isAvailabilityQuantityDisplayed(page) {
    return this.elementVisible(page, this.productAvailabilityIcon, 1000);
  }

  /**
   * Is price displayed
   * @param page
   * @returns {boolean}
   */
  isPriceDisplayed(page) {
    return this.elementVisible(page, this.productPrice, 1000);
  }

  /**
   * Is add to cart button displayed
   * @param page
   * @returns {boolean}
   */
  isAddToCartButtonDisplayed(page) {
    return this.elementVisible(page, this.addToCartButton, 1000);
  }

  /**
   * Is unavailable product size displayed
   * @param page
   * @param size
   * @returns {Promise<boolean>}
   */
  async isUnavailableProductSizeDisplayed(page, size) {
    await page.waitForTimeout(2000);
    const exist = await page.$(this.productSizeOption(size)) !== null;
    return exist;
  }

  /**
   * Is unavailable product color displayed
   * @param page
   * @param color
   * @returns {boolean}
   */
  isUnavailableProductColorDisplayed(page, color) {
    return this.elementVisible(page, this.productColorInput(color), 1000);
  }

  /**
   * Get product page URL
   * @param page
   * @returns {Promise<string>}
   */
  getProductPageURL(page) {
    return this.getAttributeContent(page, this.metaLink, 'content');
  }

  /**
   * Get discount column title
   * @param page
   * @returns {Promise<string>}
   */
  getDiscountColumnTitle(page) {
    return this.getTextContent(page, this.discountColumn);
  }

  /**
   * Get discount value
   * @param page
   * @returns {Promise<string>}
   */
  getDiscountValue(page) {
    return this.getTextContent(page, this.discountValue);
  }

  /**
   * Is add to cart button enabled
   * @param page
   * @returns {boolean}
   */
  isAddToCartButtonEnabled(page) {
    return this.elementNotVisible(page, `${this.addToCartButton}:disabled`, 1000);
  }

  /**
   * Get product availability label
   * @param page
   * @returns {Promise<string>}
   */
  getProductAvailabilityLabel(page) {
    return this.getTextContent(page, this.productAvailability, false);
  }

  /**
   * Check if delivery information text is visible
   * @param page
   * @return {boolean}
   */
  isDeliveryInformationVisible(page) {
    return this.elementVisible(page, this.deliveryInformationSpan, 1000);
  }

  /**
   * Get delivery information text
   * @param page
   * @return {Promise<string>}
   */
  getDeliveryInformationText(page) {
    return this.getTextContent(page, this.deliveryInformationSpan);
  }
}

module.exports = new Product();
