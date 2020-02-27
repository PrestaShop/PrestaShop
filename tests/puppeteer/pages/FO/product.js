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
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.proceedToCheckoutButton = '#blockcart-modal div.cart-content-btn a';
    this.productQuantitySpan = '#product-details div.product-quantities label';
    this.productDetail = 'div.product-information  a[href=\'#product-details\']';
    this.productAvailabilityIcon = '#product-availability i';
  }

  /**
   * To check the product information (Product name, price, quantity, description)
   * @param productData, product data to check
   */
  async checkProduct(productData) {
    return {
      name: await this.checkTextValue(this.productName, productData.name),
      price: await this.checkAttributeValue(this.productPrice, 'content', productData.price),
      quantity_wanted: await this.checkAttributeValue(this.productQuantity, 'value', productData.quantity_wanted),
      description: await this.checkTextValue(this.productDescription, productData.description, 'contain'),
    };
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   */
  async addProductToTheCart() {
    await this.waitForSelectorAndClick(this.addToCartButton);
    await this.waitForSelectorAndClick(this.proceedToCheckoutButton);
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
};
