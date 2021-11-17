const faker = require('faker');

/**
 * Create new cart rule to use on creation cart rule form on BO
 * @class
 */
class CartRuleData {
  /**
   * Constructor for class CartRuleData
   * @param cartRuleToCreate {Object} Could be used to force the value of some members
   */
  constructor(cartRuleToCreate = {}) {
    // Information
    /** @type {string} Name of the cart rule */
    this.name = cartRuleToCreate.name || faker.commerce.department();

    /** @type {string} Name of the cart rule */
    this.description = faker.lorem.sentence();

    /** @type {?string} Code to apply the cart rule */
    this.code = cartRuleToCreate.code || null;

    /** @type {boolean} True to generate code */
    this.generateCode = cartRuleToCreate.generateCode || false;

    /** @type {boolean} True to display cart rule highlight */
    this.highlight = cartRuleToCreate.highlight === undefined ? false : cartRuleToCreate.highlight;

    /** @type {string} True to enable partial use */
    this.partialUse = cartRuleToCreate.partialUse === undefined ? true : cartRuleToCreate.partialUse;

    /** @type {number} Priority of the cart rule */
    this.priority = cartRuleToCreate.priority || 1;

    /** @type {boolean} Status of the cart rule */
    this.status = cartRuleToCreate.status === undefined ? true : cartRuleToCreate.status;

    // Conditions
    /** @type {string|boolean} Specific customer for the cart rule or false to disable it */
    this.customer = cartRuleToCreate.customer || false;

    /** @type {string|boolean} Starting date for the cart rule or false to disable it */
    this.dateFrom = cartRuleToCreate.dateFrom || false;

    /** @type {string|boolean} Ending date for the cart rule or false to disable it */
    this.dateTo = cartRuleToCreate.dateTo || false;


    /** @type {{shipping: string, currency: string, tax: string, value: number}} Minimum amount parameters */
    this.minimumAmount = {
      /** @type {number} Value of the minimum amount */
      value: cartRuleToCreate.minimumAmount === undefined ? 0 : cartRuleToCreate.minimumAmount.value,

      /** @type {string} Currency used on minimum amount */
      currency: cartRuleToCreate.minimumAmount === undefined ? 'EUR' : cartRuleToCreate.minimumAmount.currency,

      /** @type {string} Tax used on the minimum amount */
      tax: cartRuleToCreate.minimumAmount === undefined ? 'Tax included' : cartRuleToCreate.minimumAmount.tax,

      /** @type {string} Shipping used on minimum amount */
      shipping: cartRuleToCreate.minimumAmount === undefined
        ? 'Shipping included' : cartRuleToCreate.minimumAmount.shipping,
    };

    /** @type {number} Amount of times that cart rule could be used */
    this.quantity = cartRuleToCreate.quantity || 1;

    /** @type {number} Amount of times a user can use the cart rule */
    this.quantityPerUser = cartRuleToCreate.quantityPerUser || 1;

    // Actions
    /** @type {boolean} True to enable free shipping on the cart rule */
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? false : cartRuleToCreate.freeShipping;

    /** @type {string} Discount type of the cart rule */
    this.discountType = cartRuleToCreate.discountType || 'None';

    /** @type {number|undefined} Discount percent for the cart rule */
    this.discountPercent = undefined;

    /** @type {{currency: string, tax: string, value: number}|undefined} Discount amount values for the cart rule */
    this.discountAmount = undefined;

    if (this.discountType === 'Percent') {
      this.discountPercent = cartRuleToCreate.discountPercent || faker.random.number({min: 10, max: 80});
    } else if (this.discountPercent === 'Amount') {
      this.discountAmount = {
        /** @type {number} Value of the discount amount */
        value: cartRuleToCreate.discountAmount === undefined ? 0 : cartRuleToCreate.discountAmount.value,

        /** @type {string} Currency used for the discount amount */
        currency: cartRuleToCreate.discountAmount === undefined ? 'EUR' : cartRuleToCreate.discountAmount.currency,

        /** @type {string} Tax that will be used for the discount amount */
        tax: cartRuleToCreate.discountAmount === undefined ? 'Tax included' : cartRuleToCreate.discountAmount.tax,
      };
    }

    /** @type {boolean} True to exclude discount of specific products */
    this.excludeDiscountProducts = cartRuleToCreate.excludeDiscountProducts === undefined
      ? false : cartRuleToCreate.excludeDiscountProducts;

    /** @type {boolean} True to enable free gift */
    this.freeGift = cartRuleToCreate.freeGift === undefined ? false : cartRuleToCreate.freeGift;

    if (this.freeGift) {
      /** @type {{name: string, price: number}} Product to set for the free gift */
      this.freeGiftProduct = cartRuleToCreate.freeGiftProduct;
    }
  }
}

module.exports = CartRuleData;
