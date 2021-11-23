const faker = require('faker');

/**
 * Create new tax rules group to use on tax rules group form on BO
 * @class
 */
class TaxRulesGroupData {
  /**
   * Constructor for class TaxRulesGroupData
   * @param taxRulesGroupToCreate {Object} Could be used to force the value of some members
   */
  constructor(taxRulesGroupToCreate = {}) {
    /** @type {string} Name of the tax rules group */
    this.name = (taxRulesGroupToCreate.name || `FR tax Rule ${faker.random.word()}`).substring(0, 30).trim();

    /** @type {boolean} Status of the tax rules group */
    this.enabled = taxRulesGroupToCreate.enabled === undefined ? true : taxRulesGroupToCreate.enabled;
  }
}

module.exports = TaxRulesGroupData;
