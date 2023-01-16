// Import pages
import FOBasePage from '@pages/FO/FObasePage';

// Import data
import type {ProductCombinationColorSize} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Cart extends FOBasePage {
  public readonly pageTitle: string;

  private readonly cartRuleAlreadyUsedErrorText: string;

  private readonly cartRuleAlertMessageText: string;

  private readonly alertChooseDeliveryAddressWarningtext: string;

  private readonly productItem: (number: number) => string;

  private readonly productName: (number: number) => string;

  private readonly productRegularPrice: (number: number) => string;

  private readonly productDiscountPercentage: (number: number) => string;

  private readonly productPrice: (number: number) => string;

  private readonly productTotalPrice: (number: number) => string;

  private readonly productQuantity: (number: number) => string;

  private readonly productSize: (number: number) => string;

  private readonly productColor: (number: number) => string;

  private readonly productImage: (number: number) => string;

  private readonly deleteIcon: (number: number) => string;

  private readonly itemsNumber: string;

  private readonly subtotalDiscountValueSpan: string;

  private readonly cartTotalATI: string;

  private readonly blockPromoDiv: string;

  private readonly cartSummaryLine: (line: number) => string;

  private readonly cartRuleName: (line: number) => string;

  private readonly discountValue: (line: number) => string;

  private readonly promoCodeLink: string;

  private readonly promoInput: string;

  private readonly addPromoCodeButton: string;

  private readonly promoCodeRemoveIcon: (line: number) => string;

  private readonly cartRuleAlertMessage: string;

  private readonly cartRuleChooseCarrierAlertMessageText: string;

  private readonly cartRuleCannotUseVoucherAlertMessageText: string;

  private readonly alertWarning: string;

  private readonly proceedToCheckoutButton: string;

  private readonly disabledProceedToCheckoutButton: string;

  private readonly alertPromoCode: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on cart page
   */
  constructor() {
    super();

    this.pageTitle = 'Cart';
    this.cartRuleAlreadyUsedErrorText = 'This voucher has already been used';
    this.cartRuleAlertMessageText = 'You cannot use this voucher';
    this.alertChooseDeliveryAddressWarningtext = 'You must choose a delivery address'
    + ' before applying this voucher to your order';

    // Selectors for cart page
    // Shopping cart block selectors
    this.productItem = (number: number) => `#main li:nth-of-type(${number})`;
    this.productName = (number: number) => `${this.productItem(number)} div.product-line-info a`;
    this.productRegularPrice = (number: number) => `${this.productItem(number)} span.regular-price`;
    this.productDiscountPercentage = (number: number) => `${this.productItem(number)} span.discount-percentage`;
    this.productPrice = (number: number) => `${this.productItem(number)} div.current-price span`;
    this.productTotalPrice = (number: number) => `${this.productItem(number)} span.product-price`;
    this.productQuantity = (number: number) => `${this.productItem(number)} div.input-group `
      + 'input.js-cart-line-product-quantity';
    this.productSize = (number: number) => `${this.productItem(number)} div.product-line-info.size span.value`;
    this.productColor = (number: number) => `${this.productItem(number)} div.product-line-info.color span.value`;
    this.productImage = (number: number) => `${this.productItem(number)} span.product-image img`;
    this.deleteIcon = (number: number) => `${this.productItem(number)} .remove-from-cart`;

    // Cart summary block selectors
    this.itemsNumber = '#cart-subtotal-products span.label.js-subtotal';
    this.subtotalDiscountValueSpan = '#cart-subtotal-discount span.value';
    this.cartTotalATI = '.cart-summary-totals span.value';
    this.blockPromoDiv = '.block-promo';
    this.cartSummaryLine = (line: number) => `${this.blockPromoDiv} li:nth-child(${line}).cart-summary-line`;
    this.cartRuleName = (line: number) => `${this.cartSummaryLine(line)} span.label`;
    this.discountValue = (line: number) => `${this.cartSummaryLine(line)} div span`;

    this.promoCodeLink = '#main div.block-promo a[href=\'#promo-code\']';
    this.promoInput = '#promo-code input.promo-input';
    this.addPromoCodeButton = '#promo-code button.btn-primary';
    this.promoCodeRemoveIcon = (line: number) => `${this.cartSummaryLine(line)} a[data-link-action='remove-voucher']`;
    this.cartRuleAlertMessage = '#promo-code div.alert-danger span.js-error-text';
    this.cartRuleChooseCarrierAlertMessageText = 'You must choose a carrier before applying this voucher to your order';
    this.cartRuleCannotUseVoucherAlertMessageText = 'You cannot use this voucher with this carrier';

    this.alertWarning = '.checkout.cart-detailed-actions.card-block div.alert.alert-warning';

    this.proceedToCheckoutButton = '#main div.checkout a';
    this.disabledProceedToCheckoutButton = '#main div.checkout button.disabled';

    this.alertPromoCode = '#promo-code > div > div > span';
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
    image: string|null,
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
      image: await this.getAttributeContent(page, this.productImage(row), 'src'),
      quantity: parseFloat(await this.getAttributeContent(page, this.productQuantity(row), 'value') ?? ''),
      totalPrice: await this.getPriceFromText(page, this.productTotalPrice(row)),
    };
  }

  /**
   * Get product attributes
   * @param page {Page} Browser tab
   * @param row {number} Row number in the table
   * @returns {Promise<ProductCombinationColorSize>}
   */
  async getProductAttributes(page: Page, row: number): Promise<ProductCombinationColorSize> {
    return {
      size: await this.getTextContent(page, this.productSize(row)),
      color: await this.getTextContent(page, this.productColor(row)),
    };
  }

  /**
   * Click on Proceed to checkout button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnProceedToCheckout(page: Page): Promise<void> {
    await this.waitForVisibleSelector(page, this.proceedToCheckoutButton);
    await this.clickAndWaitForNavigation(page, this.proceedToCheckoutButton);
  }

  /**
   * To edit the product quantity
   * @param page {Page} Browser tab
   * @param productID {number} ID of the product
   * @param quantity {number} New quantity of the product
   * @returns {Promise<void>}
   */
  async editProductQuantity(page: Page, productID: number, quantity: number): Promise<void> {
    await this.setValue(page, this.productQuantity(productID), quantity.toString());
    // click on price to see that its changed
    await page.click(this.productPrice(productID));
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @param productID {number} ID of the product
   * @returns {Promise<void>}
   */
  async deleteProduct(page: Page, productID: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.deleteIcon(productID));
  }

  /**
   * Get All tax included price
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getATIPrice(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.cartTotalATI, 2000);
  }

  /**
   * Get subtotal discount value
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getSubtotalDiscountValue(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.subtotalDiscountValueSpan, 2000);
  }

  /**
   * Is proceed to checkout button disabled
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isProceedToCheckoutButtonDisabled(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.disabledProceedToCheckoutButton, 1000);
  }

  /**
   * Is alert warning for minimum purchase total visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isAlertWarningForMinimumPurchaseVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.alertWarning, 1000);
  }

  /**
   * Get alert warning
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertWarning(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertWarning);
  }

  /**
   * Get alert warning
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getAlertWarningForPromoCode(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertPromoCode);
  }

  /**
   * Set promo code
   * @param page {Page} Browser tab
   * @param code {string} The promo code
   * @param clickOnPromoCodeLink {boolean} True if we need to click on promo code link
   * @returns {Promise<void>}
   */
  async addPromoCode(page: Page, code: string, clickOnPromoCodeLink: boolean = true): Promise<void> {
    if (clickOnPromoCodeLink) {
      await page.click(this.promoCodeLink);
    }
    await this.setValue(page, this.promoInput, code);
    await page.click(this.addPromoCodeButton);
  }

  /**
   * Get cart rule name
   * @param page {Page} Browser tab
   * @param line {number} Cart summary line
   * @returns {Promise<number>}
   */
  getCartRuleName(page: Page, line: number = 1): Promise<string> {
    return this.getTextContent(page, this.cartRuleName(line));
  }

  /**
   * Get cart rule error text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCartRuleErrorMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.cartRuleAlertMessage);
  }

  /**
   * Get discount value
   * @param page {Page} Browser tab
   * @param line {number} Cart summary line
   * @returns {Promise<number>}
   */
  getDiscountValue(page: Page, line: number = 1): Promise<number> {
    return this.getPriceFromText(page, this.discountValue(line), 2000);
  }

  /**
   * Remove voucher
   * @param page {Page} Browser tab
   * @param line {number} Cart summary line
   * @returns {Promise<void>}
   */
  async removeVoucher(page: Page, line: number = 1): Promise<void> {
    await this.waitForSelectorAndClick(page, this.promoCodeRemoveIcon(line));
    await this.waitForHiddenSelector(page, this.promoCodeRemoveIcon(line));
  }
}

export default new Cart();
