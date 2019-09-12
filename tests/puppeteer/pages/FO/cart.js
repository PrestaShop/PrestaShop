const CommonPage = require('../commonPage');

module.exports = class Cart extends CommonPage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Cart';

    // Selectors for cart page
    this.productName = '#main li:nth-of-type(%NUMBER) div.product-line-info > a';
    this.productPrice = '#main li:nth-of-type(%NUMBER) div.current-price > span';
    this.productQuantity = '#main li:nth-of-type(%NUMBER) div.input-group input.js-cart-line-product-quantity';
    this.proceedToCheckoutButton = '#main div.checkout a';
    this.cartTotalTTC = '.cart-summary-totals span.value';
    this.itemsNumber = '#cart-subtotal-products span.label.js-subtotal';

    // Selectors for checkout page
    this.checkoutStepOneTitle = '#checkout-personal-information-step > h1';
  }

  /**
   * To check the cart details (product name, price, quantity)
   * @param cartData, cart data to check
   * @param productID, product id to check
   */
  async checkProductInCart(cartData, productID) {
    await this.checkTextValue(this.productName.replace('%NUMBER', productID), cartData.name);
    await this.checkTextValue(this.productPrice.replace('%NUMBER', productID), cartData.price);
    await this.checkAttributeValue(this.productQuantity.replace('%NUMBER', productID), 'value', cartData.quantity);
  }

  /**
   * Click on Proceed to checkout button
   */
  async clickOnProceedToCheckout() {
    await this.waitForSelectorAndClick(this.proceedToCheckoutButton);
    await this.page.waitForSelector(this.checkoutStepOneTitle);
  }

  /**
   * To edit the product quantity
   * @param productID
   * @param quantity
   */
  async editProductQuantity(productID, quantity) {
    await this.setValue(this.productQuantity.replace('%NUMBER', productID), quantity);
  }
};
