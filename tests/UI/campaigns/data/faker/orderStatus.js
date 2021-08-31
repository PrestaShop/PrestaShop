const faker = require('faker');

/**
 * Create new order status to use on creation form on order status page on BO
 * @class
 */
class OrderStatusData {
  /**
   * Constructor for class OrderStatusData
   * @param orderStatusToCreate {Object} Could be used to force the value of some members
   */
  constructor(orderStatusToCreate = {}) {
    /** @type {string} Name of the status */
    this.name = orderStatusToCreate.name || `order_status_${faker.lorem.word()}`;

    /** @type {string} Hexadecimal value for the status color */
    this.color = orderStatusToCreate.color || faker.internet.color();

    /** @type {boolean} True to consider order is valid */
    this.logableOn = orderStatusToCreate.logableOn === undefined ? true : orderStatusToCreate.logableOn;

    /** @type {boolean} True to allow a customer to download and view PDF versions of the invoices */
    this.invoiceOn = orderStatusToCreate.invoiceOn === undefined ? true : orderStatusToCreate.invoiceOn;

    /** @type {boolean} True to hide this status in all customer orders. */
    this.hiddenOn = orderStatusToCreate.hiddenOn === undefined ? true : orderStatusToCreate.hiddenOn;

    /** @type {boolean} True to send an email to the customer when his/her order status has changed */
    this.sendEmailOn = orderStatusToCreate.sendEmailOn === undefined ? true : orderStatusToCreate.sendEmailOn;

    /** @type {boolean} True to attach invoice PDF to email */
    this.pdfInvoiceOn = orderStatusToCreate.pdfInvoiceOn === undefined ? true : orderStatusToCreate.pdfInvoiceOn;

    /** @type {boolean} True to attach delivery slip PDF to email */
    this.pdfDeliveryOn = orderStatusToCreate.pdfDeliveryOn === undefined ? true : orderStatusToCreate.pdfDeliveryOn;

    /** @type {boolean} True to set the order as shipped */
    this.shippedOn = orderStatusToCreate.shippedOn === undefined ? true : orderStatusToCreate.shippedOn;

    /** @type {boolean} True to set the order as paid */
    this.paidOn = orderStatusToCreate.paidOn === undefined ? true : orderStatusToCreate.paidOn;

    /** @type {boolean} True to show delivery PDF */
    this.deliveryOn = orderStatusToCreate.deliveryOn === undefined ? true : orderStatusToCreate.deliveryOn;
  }
}

module.exports = OrderStatusData;
