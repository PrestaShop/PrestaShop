const faker = require('faker');

module.exports = class CartRule {
  constructor(cartRuleToCreate = {}) {
    // Information
    this.name = cartRuleToCreate.name || faker.commerce.department();
    this.description = faker.lorem.sentence();
    this.code = cartRuleToCreate.code || null;
    this.generateCode = cartRuleToCreate.generateCode || false;
    this.highlight = cartRuleToCreate.highlight === undefined ? false : cartRuleToCreate.highlight;
    this.partialUse = cartRuleToCreate.partialUse === undefined ? true : cartRuleToCreate.partialUse;
    this.priority = cartRuleToCreate.priority || 1;
    this.status = cartRuleToCreate.status === undefined ? true : cartRuleToCreate.status;

    // Conditions
    this.customer = cartRuleToCreate.customer || false;
    this.dateFrom = cartRuleToCreate.dateFrom || false;
    this.dateTo = cartRuleToCreate.dateTo || false;

    this.minimumAmount = {
      value: cartRuleToCreate.minimumAmount === undefined ? 0 : cartRuleToCreate.minimumAmount.value,
      currency: cartRuleToCreate.minimumAmount === undefined ? 'EUR' : cartRuleToCreate.minimumAmount.currency,
      tax: cartRuleToCreate.minimumAmount === undefined ? 'Tax included' : cartRuleToCreate.minimumAmount.tax,
      shipping: cartRuleToCreate.minimumAmount === undefined
        ? 'Shipping included' : cartRuleToCreate.minimumAmount.shipping,
    };

    this.quantity = cartRuleToCreate.quantity || 1;
    this.quantityPerUser = cartRuleToCreate.quantityPerUser || 1;

    // Actions
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? false : cartRuleToCreate.freeShipping;

    this.discountType = cartRuleToCreate.discountType || 'None';
    if (this.discountType === 'Percent') {
      this.discountPercent = cartRuleToCreate.discountPercent || faker.random.number({min: 10, max: 80});
    } else if (this.discountPercent === 'Amount') {
      this.discountAmount = {
        value: cartRuleToCreate.discountAmount === undefined ? 0 : cartRuleToCreate.discountAmount.value,
        currency: cartRuleToCreate.discountAmount === undefined ? 'EUR' : cartRuleToCreate.discountAmount.currency,
        tax: cartRuleToCreate.discountAmount === undefined ? 'Tax included' : cartRuleToCreate.discountAmount.tax,
      };
    }

    this.excludeDiscountProducts = cartRuleToCreate.excludeDiscountProducts === undefined
      ? false : cartRuleToCreate.excludeDiscountProducts;

    this.freeGift = cartRuleToCreate.freeGift === undefined ? false : cartRuleToCreate.freeGift;
    if (this.freeGift) {
      this.freeGiftProduct = cartRuleToCreate.freeGiftProduct;
    }
  }
};
