const CommonPage = require('../commonPage');

module.exports = class FO_PRODUCT extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for product page
    this.productName = '#main h1[itemprop="name"]';
    this.productPrice = '#main span[itemprop="price"]';
    this.productQuantity = '#quantity_wanted';
    this.productDescription = '#description';
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.proceedToCheckoutButton = '#blockcart-modal div.cart-content-btn a';
  }

  /**
   * To check the product information (Product name, price, quantity, description)
   * @param productData, product data to check
   */
  async checkProduct(productData) {
    await this.checkTextValue(this.productName, productData.name_fr);
    await this.checkAttributeValue(this.productPrice, 'content', productData.price);
    await this.checkAttributeValue(this.productQuantity, 'value', productData.quantity);
    await this.checkTextValue(this.productDescription, productData.description_fr, 'contain');
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   */
  async addProductToTheCart() {
    await this.waitForSelectorAndClick(this.addToCartButton);
    await this.waitForSelectorAndClick(this.proceedToCheckoutButton, 5000);
  }
};
