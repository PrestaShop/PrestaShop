require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Cart extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Cart';

    // Selectors for cart page
    this.productItem = '#main li:nth-of-type(%NUMBER)';
    this.productName = `${this.productItem} div.product-line-info > a`;
    this.productPrice = `${this.productItem} div.current-price > span`;
    this.productQuantity = `${this.productItem} div.input-group input.js-cart-line-product-quantity`;
    this.proceedToCheckoutButton = '#main div.checkout a';
    this.cartTotalTTC = '.cart-summary-totals span.value';
    this.itemsNumber = '#cart-subtotal-products span.label.js-subtotal';
  }

  /**
   * To check the cart details (product name, price, quantity)
   * @param cartData, cart data to check
   * @param productID, product id to check
   */
  async checkProductInCart(cartData, productID) {
    return {
      name: await this.checkTextValue(this.productName.replace('%NUMBER', productID), cartData.name),
      price: await this.checkTextValue(this.productPrice.replace('%NUMBER', productID), cartData.price),
      quantity: await this.checkAttributeValue(this.productQuantity.replace('%NUMBER', productID), 'value',
        cartData.quantity),
    };
  }

  /**
   * Click on Proceed to checkout button
   */
  async clickOnProceedToCheckout() {
    await this.page.waitForSelector(this.proceedToCheckoutButton, {visible: true});
    await this.clickAndWaitForNavigation(this.proceedToCheckoutButton);
  }

  /**
   * To edit the product quantity
   * @param productID
   * @param quantity
   */
  async editProductQuantity(productID, quantity) {
    await this.setValue(this.productQuantity.replace('%NUMBER', productID), quantity.toString());
    // click on price to see that its changed
    await this.page.click(this.productPrice.replace('%NUMBER', productID));
  }

  /**
   * To get a number from text
   * @param selector
   * @param timeout
   * @return integer
   */
  async getPriceFromText(selector, timeout = 0) {
    await this.page.waitFor(timeout);
    const text = await this.getTextContent(selector);
    const number = Number(text.replace(/[^0-9.-]+/g, ''));
    return parseFloat(number);
  }
};
