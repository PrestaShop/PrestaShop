const faker = require('faker');

/**
 * Create new invoice to use on option form on invoice page on BO
 * @class
 */
class InvoiceData {
  /**
   * Constructor for class InvoiceData
   * @param invoiceOptions {Object} Could be used to force the value of some members
   */
  constructor(invoiceOptions = {}) {
    /** @member {number} Invoice number to set on form */
    this.invoiceNumber = invoiceOptions.invoiceNumber || faker.random.number({min: 100, max: 200}).toString();

    /** @member {string} legal free text to add to invoice */
    this.legalFreeText = invoiceOptions.legalFreeText || faker.lorem.sentence().substring(0, 10);

    /** @member {string} Footer text to add to form */
    this.footerText = invoiceOptions.footerText || faker.lorem.word();

    /** @member {string} Prefix of the invoice file */
    this.prefix = invoiceOptions.prefix || `#${faker.lorem.word()}`;
  }
}

module.exports = InvoiceData;
