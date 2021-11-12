const faker = require('faker');

/**
 * Create new tax to use on tax form on BO
 * @class
 */
class TaxData {
  /**
   * Constructor for class TaxData
   * @param taxToCreate {Object} Could be used to force the value of some members
   */
  constructor(taxToCreate = {}) {
    /** @type {number} Tax of the rate */
    this.rate = taxToCreate.rate || faker.random.number({min: 1, max: 40}).toString();

    /** @type {string} Name of the tax */
    this.name = taxToCreate.name || `TVA test ${this.rate}%`;

    /** @type {string} French name of the tax */
    this.frName = taxToCreate.frName || this.name;

    /** @type {boolean} Status of the tax */
    this.enabled = taxToCreate.enabled === undefined ? true : taxToCreate.enabled;
  }
}

module.exports = TaxData;
