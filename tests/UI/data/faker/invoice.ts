import InvoiceCreator from '@data/types/invoice';

import {faker} from '@faker-js/faker';

/**
 * Create new invoice to use on option form on invoice page on BO
 * @class
 */
export default class InvoiceData {
  public readonly invoiceNumber: string;

  public readonly legalFreeText: string;

  public readonly footerText: string;

  public readonly prefix: string;

  /**
   * Constructor for class InvoiceData
   * @param invoiceOptions {InvoiceCreator} Could be used to force the value of some members
   */
  constructor(invoiceOptions: InvoiceCreator = {}) {
    /** @type {number} Invoice number to set on form */
    this.invoiceNumber = invoiceOptions.invoiceNumber || faker.number.int({min: 100, max: 200}).toString();

    /** @type {string} legal free text to add to invoice */
    this.legalFreeText = invoiceOptions.legalFreeText || faker.lorem.sentence().substring(0, 10);

    /** @type {string} Footer text to add to form */
    this.footerText = invoiceOptions.footerText || faker.lorem.word();

    /** @type {string} Prefix of the invoice file */
    this.prefix = invoiceOptions.prefix || `#${faker.lorem.word()}`;
  }
}
