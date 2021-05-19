const faker = require('faker');

/**
 * Class representing order return status data
 * @class
 */
class OrderReturnStatus {
  constructor(orderReturnStatusToCreate = {}) {
    this.name = orderReturnStatusToCreate.name || `order_return_status_${faker.lorem.word()}`;
    this.color = orderReturnStatusToCreate.color || faker.internet.color();
  }
}
module.exports = OrderReturnStatus;
