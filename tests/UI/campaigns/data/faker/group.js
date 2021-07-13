const faker = require('faker');

const priceDisplayMethod = ['Tax included', 'Tax excluded'];

/**
 * Create new group to use on creation form on group page on BO
 * @class
 */
class GroupData {
  /**
   * Constructor for class GroupData
   * @param groupToCreate {Object} Could be used to force the value of some members
   */
  constructor(groupToCreate = {}) {
    /** @type {string} Name of the group */
    this.name = groupToCreate.name || faker.name.jobType();

    /** @type {string} French name of the group */
    this.frName = groupToCreate.frName || this.name;

    /** @type {string} Basic discount for the group */
    this.discount = groupToCreate.discount || 0;

    /** @type {string} Price display method of the group */
    this.priceDisplayMethod = groupToCreate.priceDisplayMethod || faker.random.arrayElement(priceDisplayMethod);

    /** @type {boolean} True to show prices for the group */
    this.shownPrices = groupToCreate.shownPrices === undefined ? true : groupToCreate.shownPrices;
  }
}

module.exports = GroupData;
