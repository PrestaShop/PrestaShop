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
   * Get Product detail from cart (product name, price, quantity)
   * @param row, product row in cart
   */
  async getProductDetail(row) {
    return {
      name: await this.getTextContent(this.productName.replace('%NUMBER', row)),
      price: await this.getTextContent(this.productPrice.replace('%NUMBER', row)),
      quantity: parseFloat(await this.getAttributeContent(this.productQuantity.replace('%NUMBER', row), 'value')),
    };
  }

  /**
   * Click on Proceed to checkout button
   */
  async clickOnProceedToCheckout() {
    await this.waitForVisibleSelector(this.proceedToCheckoutButton);
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

  /**
   * Get price TTC
   * @returns {Promise<integer>}
   */
  async getTTCPrice() {
    return this.getPriceFromText(this.cartTotalTTC);
  }
};
