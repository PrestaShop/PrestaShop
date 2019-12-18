const faker = require('faker');

module.exports = class Invoice {
  constructor(deliverySlipOptions = {}) {
    this.prefix = deliverySlipOptions.prefix || `#${faker.lorem.word()}`;
  }
};
