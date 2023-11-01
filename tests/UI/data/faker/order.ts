import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import OrderStatusData from '@data/faker/orderStatus';
import PaymentMethodData from '@data/faker/paymentMethod';
import {OrderCreator, OrderDeliveryOption, OrderProduct} from '@data/types/order';

import {faker} from '@faker-js/faker';

/**
 * Create new order message to use on creation form on order message page on BO
 * @class
 */
export default class OrderData {
  public readonly id: number;

  public reference: string;

  public readonly newClient: boolean;

  public readonly delivery: string;

  public readonly customer: CustomerData;

  public readonly totalPaid: number;

  public readonly paymentMethod: PaymentMethodData;

  public readonly status: OrderStatusData;

  public readonly deliveryAddress: AddressData;

  public readonly invoiceAddress: AddressData;

  public readonly products: OrderProduct[];

  public readonly discountGiftValue: number;

  public readonly discountPercentValue: number;

  public readonly totalPrice: number;

  public readonly deliveryOption: OrderDeliveryOption;

  /**
   * Constructor for class Order
   * @param valueToCreate {OrderCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: OrderCreator = {}) {
    /** @type {number} */
    this.id = valueToCreate.id || 0;
    /** @type {string} */
    this.reference = valueToCreate.reference || faker.string.alpha({
      casing: 'upper',
      length: 9,
    });

    /** @type {boolean} */
    this.newClient = valueToCreate.newClient === undefined ? true : valueToCreate.newClient;

    /** @type {string} */
    this.delivery = valueToCreate.delivery || 'France';

    /** @type {CustomerData} */
    this.customer = valueToCreate.customer || Customers.johnDoe;

    /** @type {number} */
    this.totalPaid = valueToCreate.totalPaid || 0;

    /** @type {PaymentMethodData} */
    this.paymentMethod = valueToCreate.paymentMethod || PaymentMethods.checkPayment;

    /** @type {OrderStatusData|null} */
    this.status = valueToCreate.status || OrderStatuses.paymentAccepted;

    /** @type {AddressData} */
    this.deliveryAddress = valueToCreate.deliveryAddress || new AddressData();

    /** @type {AddressData} */
    this.invoiceAddress = valueToCreate.invoiceAddress || new AddressData();

    /** @type {OrderProduct[]} */
    this.products = valueToCreate.products || [];

    /** @type {number} */
    this.discountGiftValue = valueToCreate.discountGiftValue || 0;

    /** @type {number} */
    this.discountPercentValue = valueToCreate.discountPercentValue || 0;

    /** @type {number} */
    this.totalPrice = valueToCreate.totalPrice || 0;

    /** @type {OrderDeliveryOption} */
    this.deliveryOption = valueToCreate.deliveryOption || {
      name: '',
      freeShipping: false,
    };
  }
}
