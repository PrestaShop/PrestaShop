import {OrderCreator, OrderDeliveryOption, OrderProduct} from '@data/types/order';

import {
  // Import data
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerAddress,
  FakerCustomer,
  type FakerOrderStatus,
  type FakerPaymentMethod,
} from '@prestashop-core/ui-testing';

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

  public readonly customer: FakerCustomer;

  public readonly totalPaid: number;

  public readonly paymentMethod: FakerPaymentMethod;

  public readonly status: FakerOrderStatus;

  public readonly deliveryAddress: FakerAddress;

  public readonly invoiceAddress: FakerAddress;

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

    /** @type {FakerCustomer} */
    this.customer = valueToCreate.customer || dataCustomers.johnDoe;

    /** @type {number} */
    this.totalPaid = valueToCreate.totalPaid || 0;

    /** @type {FakerPaymentMethod} */
    this.paymentMethod = valueToCreate.paymentMethod || dataPaymentMethods.checkPayment;

    /** @type {FakerOrderStatus|null} */
    this.status = valueToCreate.status || dataOrderStatuses.paymentAccepted;

    /** @type {FakerCustomer} */
    this.deliveryAddress = valueToCreate.deliveryAddress || new FakerAddress();

    /** @type {FakerCustomer} */
    this.invoiceAddress = valueToCreate.invoiceAddress || new FakerAddress();

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
