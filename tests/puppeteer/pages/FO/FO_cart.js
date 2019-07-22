const CommonPage = require('../commonPage');

module.exports = class FO_CART extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for cart page
    this.productName = '#main li:nth-of-type(%NUMBER) div.product-line-info > a';
    this.productPrice = '#main li:nth-of-type(%NUMBER) div.current-price > span';
    this.productQuantity = '#main li:nth-of-type(%NUMBER) div.input-group input.js-cart-line-product-quantity';
    this.proceedToCheckoutButton = '#main div.checkout a';

    // Selectors for checkout page
    this.checkoutStepOneTitle = '#checkout-personal-information-step > h1';
  }

  /**
   * To check the cart details (product name, price, quantity)
   */
  async checkCartDetails(cartData, productID) {
    await this.checkTextValue(this.productName.replace('%NUMBER', productID), cartData.name_fr);
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
};
