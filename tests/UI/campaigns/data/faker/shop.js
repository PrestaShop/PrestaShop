const faker = require('faker');

module.exports = class ShopGroup {
  constructor(shopToCreate) {
    this.name = shopToCreate.name || `shop_${faker.lorem.word()}`;
    this.shopGroup = shopToCreate.shopGroup;
    this.categoryRoot = shopToCreate.categoryRoot;
  }
};
