// Import FO pages
import {CartPage} from '@pages/FO/classic/cart/index';
import type {Page} from 'playwright';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Cart extends CartPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    this.proceedToCheckoutButton = '#wrapper div.cart-summary div.checkout a.btn';
    this.noItemsInYourCartSpan = '#content-wrapper div.cart-overview p';
    this.productItem = (number: number) => `#content-wrapper li.cart__item:nth-of-type(${number})`;
    this.productName = (number: number) => `${this.productItem(number)} div.product-line__content a.product-line__title`;
    this.productRegularPrice = (number: number) => `${this.productItem(number)} div.product-line__basic`
      + ' span.product-line__regular';
    this.productDiscountPercentage = (number: number) => `${this.productItem(number)} span.discount.badge.discount`;
    this.productPrice = (number: number) => `${this.productItem(number)} div.product-line__current span.price`;
    this.productTotalPrice = (number: number) => `${this.productItem(number)} span.product-line__price`;
    this.productQuantity = (number: number) => `${this.productItem(number)} div.input-group `
      + 'input.js-cart-line-product-quantity';
    this.productQuantityScrollUpButton = (number: number) => `${this.productItem(number)} button.js-increment-button`;
    this.productQuantityScrollDownButton = (number: number) => `${this.productItem(number)} button.js-decrement-button`;
    this.productImage = (number: number) => `${this.productItem(number)} div.product-line__image img`;
    this.productSize = (number: number) => `${this.productItem(number)} div.product-line__info.size span.value`;
    this.productColor = (number: number) => `${this.productItem(number)} div.product-line__info.color span.value`;

    // Promo code selectors
    this.promoCodeLink = 'div.cart-voucher button.accordion-button';
    this.promoInput = '#promo-code input[name="discount_name"]';
    this.cartSummaryLine = (line: number) => `div.cart-voucher li:nth-child(${line})`;
    this.cartRuleName = (line: number) => `${this.cartSummaryLine(line)} span.cart-voucher__name`;
    this.discountValue = (line: number) => `${this.cartSummaryLine(line)} div span.fw-bold`;

    // Notifications
    this.alertMessage = '#js-toast-container div.toast div.toast-body';
  }

  /**
   * Get Product detail from cart
   * @param page {Page} Browser tab
   * @param row {number} Row number in the table
   * @returns {Promise<{discountPercentage: string, image: string|null, quantity: number, totalPrice: number,
   *     price: number, regularPrice: number, name: string}>}
   */
  async getProductDetail(page: Page, row: number): Promise<{
    discountPercentage: string,
    image: string | null,
    quantity: number,
    totalPrice: number,
    price: number,
    regularPrice: number,
    name: string,
  }> {
    return {
      name: await this.getTextContent(page, this.productName(row)),
      regularPrice: await this.getPriceFromText(page, this.productRegularPrice(row)),
      price: await this.getPriceFromText(page, this.productPrice(row)),
      discountPercentage: await this.getTextContent(page, this.productDiscountPercentage(row)),
      image: await this.getAttributeContent(page, this.productImage(row), 'srcset'),
      quantity: parseFloat(await this.getAttributeContent(page, this.productQuantity(row), 'value') ?? ''),
      totalPrice: await this.getPriceFromText(page, this.productTotalPrice(row)),
    };
  }

  /**
   * Set quantity
   * @param page {Page} Browser tab
   * @param productID {number} Row of the product
   * @param quantity {number} New quantity of the product
   * @returns {Promise<void>}
   */
  async setQuantity(page: Page, productID: number, quantity: number | string): Promise<void> {
    await this.setValue(page, this.productQuantity(productID), quantity);
  }

  /**
   * To edit the product quantity
   * @param page {Page} Browser tab
   * @param productID {number} Row of the product
   * @param quantity {number} New quantity of the product
   * @returns {Promise<void>}
   */
  async editProductQuantity(page: Page, productID: number, quantity: number | string): Promise<void> {
    await this.setValue(page, this.productQuantity(productID), quantity);
    await page.locator(this.productQuantityScrollUpButton(productID)).click();
  }
}

export default new Cart();
