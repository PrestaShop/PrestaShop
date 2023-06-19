import DeliverySlipOptionsCreator from '@data/types/deliverySlipOptions';

import {faker} from '@faker-js/faker';

/**
 * Create new delivery slip to use on creation form on delivery slip page on BO
 * @class
 */
export default class DeliverySlipOptionsData {
  public readonly prefix: string;

  public readonly number: number;

  /**
   * Constructor for class DeliverySlipData
   * @param deliverySlipOptions {DeliverySlipOptionsCreator} Could be used to force the value of some members
   */
  constructor(deliverySlipOptions: DeliverySlipOptionsCreator = {}) {
    /** @type {string} Prefix to add to the delivery slip files */
    this.prefix = deliverySlipOptions.prefix || `#${faker.lorem.word()}`;

    /** @type {number} Number of delivery slips created */
    this.number = deliverySlipOptions.number || faker.number.int({min: 10, max: 200});
  }
}
