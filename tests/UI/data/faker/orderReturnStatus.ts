import OrderReturnStatusCreator from '@data/types/orderReturnStatus';

import {faker} from '@faker-js/faker';

/**
 * Create new order return status to use on creation form on order return status page on BO
 * @class
 */
export default class OrderReturnStatusData {
  public readonly id: number;

  public readonly name: string;

  public readonly color: string;

  /**
   * Constructor for class OrderReturnStatusData
   * @param orderReturnStatusToCreate {Object} Could be used to force the value of some members
   */
  constructor(orderReturnStatusToCreate: OrderReturnStatusCreator = {}) {
    /** @type {number} ID of the status */
    this.id = orderReturnStatusToCreate.id || 0;

    /** @type {string} Name of the status (Max 32 characters) */
    this.name = (orderReturnStatusToCreate.name || `order_return_status_${faker.lorem.word({
      length: {min: 1, max: 12},
    })}`).substring(0, 32);

    /** @type {string} Hexadecimal value for the status  */
    this.color = orderReturnStatusToCreate.color || faker.internet.color();
  }
}
