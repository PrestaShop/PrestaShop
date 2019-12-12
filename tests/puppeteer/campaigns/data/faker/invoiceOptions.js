const faker = require('faker');

module.exports = class Invoice {
  constructor(invoiceOptions = {}) {
    this.prefix = invoiceOptions.prefix || `#${faker.lorem.word()}`;
  }
};
