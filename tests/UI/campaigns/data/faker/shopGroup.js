const faker = require('faker');

module.exports = class ShopGroup {
  constructor(shopGroupToCreate) {
    this.name = shopGroupToCreate.name || `shop_group_${faker.lorem.word()}`;
    this.shareCustomer = shopGroupToCreate.shareCustomer === undefined ? true : shopGroupToCreate.shareCustomer;
    this.shareAvailableQuantities = shopGroupToCreate.shareAvailableQuantities
    === undefined ? true : shopGroupToCreate.shareAvailableQuantities;
    this.status = shopGroupToCreate.status === undefined ? true : shopGroupToCreate.status;
  }
};
