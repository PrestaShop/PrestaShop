const faker = require('faker');

/**
 * Create new shop to use on shop creation form on BO
 * @class
 */
class ShopData {
  /**
   * Constructor for class ShopGroupData
   * @param shopToCreate {Object} Could be used to force the value of some members
   */
  constructor(shopToCreate = {}) {
    /** @type {string} Name of the shop */
    this.name = shopToCreate.name || `shop_${faker.lorem.word()}`;

    /** @type {string} Shop group chosen from list */
    this.shopGroup = shopToCreate.shopGroup;

    /** @type {string} Root category of the shop */
    this.categoryRoot = shopToCreate.categoryRoot;
  }
}

module.exports = ShopData;
