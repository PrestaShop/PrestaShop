require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Cart extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Cart';

    // Selectors for cart page
    this.cartGridBlock = 'div.cart-grid';
    this.productItem = number => `#main li:nth-of-type(${number})`;
    this.productName = number => `${this.productItem(number)} div.product-line-info a`;
    this.productRegularPrice = number => `${this.productItem(number)} span.regular-price`;
    this.productDiscountPercentage = number => `${this.productItem(number)} span.discount-percentage`;
    this.productPrice = number => `${this.productItem(number)} div.current-price span`;
    this.productTotalPrice = number => `${this.productItem(number)} span.product-price`;
    this.productQuantity = number => `${this.productItem(number)} div.input-group input.js-cart-line-product-quantity`;
    this.productSize = number => `${this.productItem(number)} div.product-line-info.size span.value`;
    this.productColor = number => `${this.productItem(number)} div.product-line-info.color span.value`;
    this.productImage = number => `${this.productItem(number)} span.product-image img`;
    this.deleteIcon = number => `${this.productItem(number)} .remove-from-cart`;
    this.proceedToCheckoutButton = '#main div.checkout a';
    this.disabledProceedToCheckoutButton = '#main div.checkout button.disabled';
    this.subtotalDiscountValueSpan = '#cart-subtotal-discount span.value';
    this.cartTotalATI = '.cart-summary-totals span.value';
    this.itemsNumber = '#cart-subtotal-products span.label.js-subtotal';
    this.alertWarning = '.checkout.cart-detailed-actions.card-block div.alert.alert-warning';
    this.promoCodeLink = '#main div.block-promo a[href=\'#promo-code\']';
    this.promoInput = '#promo-code input.promo-input';
    this.addPromoCodeButton = '#promo-code button.btn-primary';
  }

  /**
   * Get Product detail from cart
   * @param page
   * @param row
   * @returns {Promise<{discountPercentage: *, image: *, quantity: number, size: *, color: *, totalPrice: *,
   * price: number, regularPrice: number, name: *}>}
   */
  async getProductDetail(page, row) {
    return {
      name: await this.getTextContent(page, this.productName(row)),
      regularPrice: await this.getPriceFromText(page, this.productRegularPrice(row)),
      price: await this.getPriceFromText(page, this.productPrice(row)),
      discountPercentage: await this.getTextContent(page, this.productDiscountPercentage(row)),
      image: await this.getAttributeContent(page, this.productImage(row), 'src'),
      quantity: parseFloat(await this.getAttributeContent(page, this.productQuantity(row), 'value')),
      totalPrice: await this.getPriceFromText(page, this.productTotalPrice(row)),
    };
  }

  /**
   * Get product attributes
   * @param page
   * @param row
   * @returns {Promise<{size: *, color: *}>}
   */
  async getProductAttributes(page, row) {
    return {
      size: await this.getTextContent(page, this.productSize(row)),
      color: await this.getTextContent(page, this.productColor(row)),
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
   * Delete product
   * @param page
   * @param productID
   * @returns {Promise<void>}
   */
  async deleteProduct(page, productID) {
    await this.waitForSelectorAndClick(page, this.deleteIcon(productID));
  }

  /**
   * Get All tax included price
   * @param page
   * @returns {Promise<number>}
   */
  getATIPrice(page) {
    return this.getPriceFromText(page, this.cartTotalATI, 2000);
  }

  /**
   * Get subtotal discount value
   * @param page
   * @returns {Promise<number>}
   */
  getSubtotalDiscountValue(page) {
    return this.getPriceFromText(page, this.subtotalDiscountValueSpan, 2000);
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

  /**
   * Set promo code
   * @param page
   * @param code
   * @returns {Promise<void>}
   */
  async addPromoCode(page, code) {
    await page.click(this.promoCodeLink);
    await this.setValue(page, this.promoInput, code);
    await page.click(this.addPromoCodeButton);
  }
}

module.exports = new Cart();
