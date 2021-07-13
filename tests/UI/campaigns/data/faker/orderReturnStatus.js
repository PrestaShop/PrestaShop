const faker = require('faker');

/**
 * Create new order return status to use on creation form on order return status page on BO
 * @class
 */
class OrderReturnStatusData {
  /**
   * Constructor for class OrderReturnStatusData
   * @param orderReturnStatusToCreate {Object} Could be used to force the value of some members
   */
  constructor(orderReturnStatusToCreate = {}) {
    /** @type {string} Name of the status */
    this.name = orderReturnStatusToCreate.name || `order_return_status_${faker.lorem.word()}`;

    /** @type {string} Hexadecimal value for the status  */
    this.color = orderReturnStatusToCreate.color || faker.internet.color();
  }
}

module.exports = OrderReturnStatusData;
