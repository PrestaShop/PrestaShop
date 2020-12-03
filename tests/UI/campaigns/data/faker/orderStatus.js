const faker = require('faker');

module.exports = class OrderStatus {
  constructor(orderStatusToCreate = {}) {
    this.name = orderStatusToCreate.name || `order_status_${faker.lorem.word()}`;
    this.color = orderStatusToCreate.color || faker.internet.color();
    this.logableOn = orderStatusToCreate.logableOn === undefined ? true : orderStatusToCreate.logableOn;
    this.invoiceOn = orderStatusToCreate.invoiceOn === undefined ? true : orderStatusToCreate.invoiceOn;
    this.hiddenOn = orderStatusToCreate.hiddenOn === undefined ? true : orderStatusToCreate.hiddenOn;
    this.sendEmailOn = orderStatusToCreate.sendEmailOn === undefined ? true : orderStatusToCreate.sendEmailOn;
    this.pdfInvoiceOn = orderStatusToCreate.pdfInvoiceOn === undefined ? true : orderStatusToCreate.pdfInvoiceOn;
    this.pdfDeliveryOn = orderStatusToCreate.pdfDeliveryOn === undefined ? true : orderStatusToCreate.pdfDeliveryOn;
    this.shippedOn = orderStatusToCreate.shippedOn === undefined ? true : orderStatusToCreate.shippedOn;
    this.paidOn = orderStatusToCreate.paidOn === undefined ? true : orderStatusToCreate.paidOn;
    this.deliveryOn = orderStatusToCreate.deliveryOn === undefined ? true : orderStatusToCreate.deliveryOn;
  }
};
