require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Product extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for product page
    this.productName = '#main h1[itemprop="name"]';
    this.productPrice = '#main span[itemprop="price"]';
    this.productQuantity = '#quantity_wanted';
    this.productDescription = '#description';
    this.colorInput = '#group_2 li input[title=%COLOR]';
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.blockCartModal = '#blockcart-modal';
    this.proceedToCheckoutButton = `${this.blockCartModal} div.cart-content-btn a`;
    this.productQuantitySpan = '#product-details div.product-quantities label';
    this.productDetail = 'div.product-information a[href=\'#product-details\']';
    this.continueShoppingButton = `${this.blockCartModal} div.cart-content-btn button`;
    this.productAvailabilityIcon = '#product-availability i';
    this.productSizeOption = '#group_1 option[title=\'%SIZE\']';
    this.productColorInput = '#group_2 input[title=\'%COLOR\']';
  }

  /**
   * Get Product information (Product name, price, description)
   * @param productData, product data to check
   */
  async getProductInformation(productData) {
    return {
      name: await this.getTextContent(this.productName),
      price: parseFloat(await this.getAttributeContent(this.productPrice, 'content')),
      description: await this.getTextContent(this.productDescription),
    };
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   * @param quantity
   * @param attributeToChoose
   * @param proceedToCheckout
   * @returns {Promise<void>}
   */
  async addProductToTheCart(quantity = 1, attributeToChoose = '', proceedToCheckout = true) {
    await this.page.waitFor(1000);
    if (attributeToChoose.color) {
      await Promise.all([
        this.page.waitForSelector(this.colorInput.replace('%COLOR', attributeToChoose.color), {visible: true}),
        this.page.click(this.colorInput.replace('%COLOR', attributeToChoose.color)),
      ]);
    }
    if (quantity !== 1) {
      await this.setValue(this.productQuantity, attributeToChoose.quantity.toString());
    }
    await this.waitForSelectorAndClick(this.addToCartButton);
    await this.page.waitForSelector(`${this.blockCartModal}[style*='display: block;']`);
    if (proceedToCheckout) {
      await this.page.waitForSelector(this.proceedToCheckoutButton, {visible: true});
      await this.clickAndWaitForNavigation(this.proceedToCheckoutButton);
    } else {
      await this.waitForSelectorAndClick(this.continueShoppingButton);
      await this.page.waitForSelector(this.continueShoppingButton, {hidden: true});
    }
  }

  /**
   * Is quantity displayed
   * @returns {Promise<boolean|true>}
   */
  async isQuantityDisplayed() {
    await this.waitForSelectorAndClick(this.productDetail);
    return this.elementVisible(this.productQuantitySpan, 1000);
  }

  /**
   * Is availability product displayed
   * @returns {boolean}
   */
  isAvailabilityQuantityDisplayed() {
    return this.elementVisible(this.productAvailabilityIcon, 1000);
  }

  /**
   * Is price displayed
   * @returns {boolean}
   */
  isPriceDisplayed() {
    return this.elementVisible(this.productPrice, 1000);
  }

  /**
   * Is add to cart button displayed
   * @returns {boolean}
   */
  isAddToCartButtonDisplayed() {
    return this.elementVisible(this.addToCartButton, 1000);
  }

  /**
   * Is unavailable product size displayed
   * @param size
   * @returns {Promise<boolean>}
   */
  async isUnavailableProductSizeDisplayed(size) {
    await this.page.waitFor(2000);
    const exist = await this.page.$(this.productSizeOption.replace('%SIZE', size)) !== null;
    return exist;
  }

  /**
   * Is unavailable product color displayed
   * @param color
   * @returns {boolean}
   */
  isUnavailableProductColorDisplayed(color) {
    return this.elementVisible(this.productColorInput.replace('%COLOR', color), 1000);
  }
};
