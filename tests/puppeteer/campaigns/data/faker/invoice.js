const faker = require('faker');

module.exports = class Invoice {
  constructor(invoiceOptions = {}) {
    this.invoiceNumber = invoiceOptions.invoiceNumber || faker.random.number;
    this.legalFreeText = invoiceOptions.legalFreeText || faker.lorem.sentence();
    this.footerText = invoiceOptions.footerText || faker.lorem.word();
  }
};
