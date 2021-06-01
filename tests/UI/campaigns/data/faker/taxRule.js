const faker = require('faker');

const {behaviour} = require('@data/demo/taxRule');
const {tax} = require('@data/demo/tax');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);

/**
 * Create new tax rule to use on tax rule form on BO
 * @class
 */
class TaxRuleData {
  /**
   * Constructor for class TaxRuleData
   * @param taxRulesToCreate {Object} Could be used to force the value of some members
   */
  constructor(taxRulesToCreate = {}) {
    /** @member {string} Country to apply the tax */
    this.country = taxRulesToCreate.country || faker.random.arrayElement(countriesNames);

    /** @member {string} Postal code of the country */
    this.zipCode = taxRulesToCreate.zipCode || faker.address.zipCode();

    /** @member {string} Behavior of the tax rule */
    this.behaviour = taxRulesToCreate.behaviour || faker.random.arrayElement(behaviour);

    /** @member {string} Name of the tax to use on the rule */
    this.tax = taxRulesToCreate.tax || tax.DefaultFrTax.name;

    /** @member {string} Description of the tax rule */
    this.description = taxRulesToCreate.description || faker.lorem.sentence();
  }
}

module.exports = TaxRuleData;
