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
    /** @member {number} Tax of the rate */
    this.rate = taxToCreate.rate || faker.random.number({min: 1, max: 40}).toString();

    /** @member {string} Name of the tax */
    this.name = taxToCreate.name || `TVA test ${this.rate}%`;

    /** @member {string} French name of the tax */
    this.frName = taxToCreate.frName || this.name;

    /** @member {boolean} Status of the tax */
    this.enabled = taxToCreate.enabled === undefined ? true : taxToCreate.enabled;
  }
}

module.exports = TaxData;
