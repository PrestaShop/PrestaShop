const faker = require('faker');

module.exports = class OrderReturnStatus {
  constructor(orderReturnStatusToCreate = {}) {
    this.name = orderReturnStatusToCreate.name || `order_return_status_${faker.lorem.word()}`;
    this.color = orderReturnStatusToCreate.color || faker.internet.color();
  }
};
