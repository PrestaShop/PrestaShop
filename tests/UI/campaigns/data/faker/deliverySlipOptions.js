const faker = require('faker');

module.exports = class Invoice {
  constructor(deliverySlipOptions = {}) {
    this.prefix = deliverySlipOptions.prefix || `#${faker.lorem.word()}`;
    this.number = deliverySlipOptions.number || faker.random.number({min: 10, max: 200}).toString();
  }
};
