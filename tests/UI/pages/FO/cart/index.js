require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Cart extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart';

    // Selectors for cart page
    this.productItem = number => `#main li:nth-of-type(${number})`;
    this.productName = number => `${this.productItem(number)} div.product-line-info > a`;
    this.productPrice = number => `${this.productItem(number)} div.current-price > span`;
    this.productQuantity = number => `${this.productItem(number)} div.input-group input.js-cart-line-product-quantity`;
    this.proceedToCheckoutButton = '#main div.checkout a';
    this.disabledProceedToCheckoutButton = '#main div.checkout button.disabled';
    this.cartTotalTTC = '.cart-summary-totals span.value';
    this.itemsNumber = '#cart-subtotal-products span.label.js-subtotal';
    this.alertWarning = '.checkout.cart-detailed-actions.card-block div.alert.alert-warning';
  }

  /**
   * Get Product detail from cart (product name, price, quantity)
   * @param page
   * @param row, product row in cart
   * @returns {Promise<{quantity: (number), price: (string), name: (string)}>}
   */
  async getProductDetail(page, row) {
    return {
      name: await this.getTextContent(page, this.productName(row)),
      price: await this.getTextContent(page, this.productPrice(row)),
      quantity: parseFloat(await this.getAttributeContent(page, this.productQuantity(row), 'value')),
    };
  }

  /**
   * Click on Proceed to checkout button
   * @param page
   * @returns {Promise<void>}
   */
  async clickOnProceedToCheckout(page) {
    await this.waitForVisibleSelector(page, this.proceedToCheckoutButton);
    await this.clickAndWaitForNavigation(page, this.proceedToCheckoutButton);
  }

  /**
   * To edit the product quantity
   * @param page
   * @param productID
   * @param quantity
   * @returns {Promise<void>}
   */
  async editProductQuantity(page, productID, quantity) {
    await this.setValue(page, this.productQuantity(productID), quantity.toString());
    // click on price to see that its changed
    await page.click(this.productPrice(productID));
  }

  /**
   * Get a number from text
   * @param page
   * @param selector
   * @param timeout
   * @returns {Promise<number>}
   */
  async getPriceFromText(page, selector, timeout = 0) {
    await page.waitForTimeout(timeout);
    const text = await this.getTextContent(page, selector);
    const number = Number(text.replace(/[^0-9.-]+/g, ''));
    return parseFloat(number);
  }

  /**
   * Get price TTC
   * @param page
   * @returns {Promise<number>}
   */
  async getTTCPrice(page) {
    return this.getPriceFromText(page, this.cartTotalTTC);
  }

  /**
   * Is proceed to checkout button disabled
   * @param page
   * @returns {boolean}
   */
  isProceedToCheckoutButtonDisabled(page) {
    return this.elementVisible(page, this.disabledProceedToCheckoutButton, 1000);
  }

  /**
   * Is alert warning for minimum purchase total visible
   * @param page
   * @returns {boolean}
   */
  isAlertWarningForMinimumPurchaseVisible(page) {
    return this.elementVisible(page, this.alertWarning, 1000);
  }

  /**
   * Get alert warning
   * @param page
   * @returns {Promise<string>}
   */
  getAlertWarning(page) {
    return this.getTextContent(page, this.alertWarning);
  }
}

module.exports = new Cart();
