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
    /** @member {string} Name of the cart rule */
    this.name = cartRuleToCreate.name || faker.commerce.department();

    /** @member {string} Name of the cart rule */
    this.description = faker.lorem.sentence();

    /** @member {string} Code to apply the cart rule */
    this.code = cartRuleToCreate.code;

    /** @member {boolean} True to generate code */
    this.generateCode = cartRuleToCreate.generateCode || false;

    /** @member {boolean} True to display cart rule gighlight */
    this.highlight = cartRuleToCreate.highlight === undefined ? false : cartRuleToCreate.highlight;

    /** @member {string} True to enable partial use */
    this.partialUse = cartRuleToCreate.partialUse === undefined ? true : cartRuleToCreate.partialUse;

    /** @member {number} Priority of the cart rule */
    this.priority = cartRuleToCreate.priority || 1;

    /** @member {boolean} Status of the cart rule */
    this.status = cartRuleToCreate.status === undefined ? true : cartRuleToCreate.status;

    // Conditions
    /** @member {string|boolean} Specific customer for the cart rule or false to disable it */
    this.customer = cartRuleToCreate.customer || false;

    /** @member {string|boolean} Starting date for the cart rule or false to disable it */
    this.dateFrom = cartRuleToCreate.dateFrom || false;

    /** @member {string|boolean} Ending date for the cart rule or false to disable it */
    this.dateTo = cartRuleToCreate.dateTo || false;


    /** @member {{shipping: string, currency: string, tax: string, value: number}} Minimum amount parameters */
    this.minimumAmount = {
      /** @member {number} Value of the minimum amount */
      value: cartRuleToCreate.minimumAmount === undefined ? 0 : cartRuleToCreate.minimumAmount.value,

      /** @member {string} Currency used on minimum amount */
      currency: cartRuleToCreate.minimumAmount === undefined ? 'EUR' : cartRuleToCreate.minimumAmount.currency,

      /** @member {string} Tax used on the minimum amount */
      tax: cartRuleToCreate.minimumAmount === undefined ? 'Tax included' : cartRuleToCreate.minimumAmount.tax,

      /** @member {string} Shipping used on minimum amount */
      shipping: cartRuleToCreate.minimumAmount === undefined
        ? 'Shipping included' : cartRuleToCreate.minimumAmount.shipping,
    };

    /** @member {number} Amount of times that cart rule could be used */
    this.quantity = cartRuleToCreate.quantity || 1;

    /** @member {number} Amount of times a user can use the cart rule */
    this.quantityPerUser = cartRuleToCreate.quantityPerUser || 1;

    // Actions
    /** @member {boolean} True to enable free shipping on the cart rule */
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? false : cartRuleToCreate.freeShipping;

    /** @member {string} Discount type of the cart rule */
    this.discountType = cartRuleToCreate.discountType || 'None';

    /** @member {number|undefined} Discount percent for the cart rule */
    this.discountPercent = undefined;

    /** @member {{currency: string, tax: string, value: number}|undefined} Discount amount values for the cart rule */
    this.discountAmount = undefined;

    if (this.discountType === 'Percent') {
      this.discountPercent = cartRuleToCreate.discountPercent || faker.random.number({min: 10, max: 80});
    } else if (this.discountPercent === 'Amount') {
      this.discountAmount = {
        /** @member {number} Value of the discount amount */
        value: cartRuleToCreate.discountAmount === undefined ? 0 : cartRuleToCreate.discountAmount.value,

        /** @member {string} Currency used for the discount amount */
        currency: cartRuleToCreate.discountAmount === undefined ? 'EUR' : cartRuleToCreate.discountAmount.currency,

        /** @member {string} Tax that will be used for the discount amount */
        tax: cartRuleToCreate.discountAmount === undefined ? 'Tax included' : cartRuleToCreate.discountAmount.tax,
      };
    }

    /** @member {boolean} True to exclude discount of specific products */
    this.excludeDiscountProducts = cartRuleToCreate.excludeDiscountProducts === undefined
      ? false : cartRuleToCreate.excludeDiscountProducts;

    /** @member {boolean} True to enable free gift */
    this.freeGift = cartRuleToCreate.freeGift === undefined ? false : cartRuleToCreate.freeGift;

    if (this.freeGift) {
      /** @member {string} Product to set for the free gift */
      this.freeGiftProduct = cartRuleToCreate.freeGiftProduct;
    }
  }
}

module.exports = CartRuleData;
