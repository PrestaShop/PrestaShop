const faker = require('faker');

module.exports = class CartRule {
  constructor(cartRuleToCreate = {}) {
    this.name = cartRuleToCreate.name || faker.commerce.department();
    this.description = faker.lorem.sentence();
    this.code = cartRuleToCreate.code;
    this.customer = cartRuleToCreate.customer || faker.internet.email();
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? 'on' : cartRuleToCreate.freeShipping;
  }
};
