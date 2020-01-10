const faker = require('faker');

module.exports = class Invoice {
  constructor(invoiceOptions = {}) {
    this.invoiceNumber = invoiceOptions.invoiceNumber || faker.random.number({min: 20, max: 200}).toString();
    this.legalFreeText = invoiceOptions.legalFreeText || faker.lorem.sentence();
    this.footerText = invoiceOptions.footerText || faker.lorem.word();
    this.prefix = invoiceOptions.prefix || `#${faker.lorem.word()}`;
  }
};
