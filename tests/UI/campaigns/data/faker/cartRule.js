const faker = require('faker');

module.exports = class CartRule {
  constructor(cartRuleToCreate = {}) {
    this.name = cartRuleToCreate.name || faker.commerce.department();
    this.description = faker.lorem.sentence();
    this.code = cartRuleToCreate.code;
    this.customer = cartRuleToCreate.customer || faker.internet.email();
    this.freeShipping = cartRuleToCreate.freeShipping === undefined ? 'on' : cartRuleToCreate.freeShipping;
    this.percent = cartRuleToCreate.percent === undefined ? true : cartRuleToCreate.percent;
    this.value = cartRuleToCreate.value || faker.random.number({min: 1, max: 100});
  }
};
