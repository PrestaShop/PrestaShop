const faker = require('faker');

module.exports = class Invoice {
  constructor(invoiceOptions = {}) {
    this.invoiceNumber = invoiceOptions.invoiceNumber || faker.random.number({min: 100, max: 200}).toString();
    this.legalFreeText = invoiceOptions.legalFreeText || faker.lorem.sentence().substring(0, 50);
    this.footerText = invoiceOptions.footerText || faker.lorem.word();
    this.prefix = invoiceOptions.prefix || `#${faker.lorem.word()}`;
  }
};
