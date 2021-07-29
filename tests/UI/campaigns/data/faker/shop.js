const faker = require('faker');

/**
 * Create new shop to use on shop creation form on BO
 * @class
 */
class ShopGroupData {
  /**
   * Constructor for class ShopGroupData
   * @param shopToCreate {Object} Could be used to force the value of some members
   */
  constructor(shopToCreate = {}) {
    /** @member {string} Name of the shop */
    this.name = shopToCreate.name || `shop_${faker.lorem.word()}`;

    /** @member {string} Shop group chosen from list */
    this.shopGroup = shopToCreate.shopGroup;

    /** @member {string} Root category of the shop */
    this.categoryRoot = shopToCreate.categoryRoot;
  }
}

module.exports = ShopGroupData;
