import OrderStatusCreator from '@data/types/orderStatus';

import {faker} from '@faker-js/faker';

/**
 * Create new order status to use on creation form on order status page on BO
 * @class
 */
export default class OrderStatusData {
  public readonly id: number;

  public readonly name: string;

  public readonly color: string;

  public readonly logableOn: boolean;

  public readonly invoiceOn: boolean;

  public readonly hiddenOn: boolean;

  public readonly sendEmailOn: boolean;

  public readonly pdfInvoiceOn: boolean;

  public readonly pdfDeliveryOn: boolean;

  public readonly shippedOn: boolean;

  public readonly paidOn: boolean;

  public readonly deliveryOn: boolean;

  public readonly emailTemplate: string;

  /**
   * Constructor for class OrderStatusData
   * @param orderStatusToCreate {OrderStatusCreator} Could be used to force the value of some members
   */
  constructor(orderStatusToCreate: OrderStatusCreator = {}) {
    /** @type {number} Name of the status */
    this.id = orderStatusToCreate.id || 0;

    /** @type {string} Name of the status (Max 32 characters) */
    this.name = (orderStatusToCreate.name || `order_status_${faker.lorem.word({
      length: {min: 1, max: 19},
    })}`).substring(0, 32);

    /** @type {string} Hexadecimal value for the status color */
    this.color = orderStatusToCreate.color || faker.internet.color();

    /** @type {boolean} True to consider order is valid */
    this.logableOn = orderStatusToCreate.logableOn === undefined ? true : orderStatusToCreate.logableOn;

    /** @type {boolean} True to allow a customer to download and view PDF versions of the invoices */
    this.invoiceOn = orderStatusToCreate.invoiceOn === undefined ? true : orderStatusToCreate.invoiceOn;

    /** @type {boolean} True to hide this status in all customer orders. */
    this.hiddenOn = orderStatusToCreate.hiddenOn === undefined ? true : orderStatusToCreate.hiddenOn;

    /** @type {boolean} True to email the customer when his/her order status has changed */
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

    /** @type {string} Email Template */
    this.emailTemplate = orderStatusToCreate.emailTemplate === undefined ? '' : orderStatusToCreate.emailTemplate;
  }
}
