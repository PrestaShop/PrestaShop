const faker = require('faker');

const {countries} = require('@data/demo/countries');
const {behaviour} = require('@data/demo/taxRule');
const {tax} = require('@data/demo/tax');

module.exports = class Tax {
  constructor(taxRulesToCreate = {}) {
    this.country = taxRulesToCreate.country || faker.random.arrayElement(countries);
    this.zipCode = taxRulesToCreate.zipCode || faker.address.zipCode();
    this.behaviour = taxRulesToCreate.behaviour || faker.random.arrayElement(behaviour);
    this.tax = taxRulesToCreate.tax || tax.DefaultFrTax.name;
    this.description = taxRulesToCreate.description || faker.lorem.sentence();
  }
};
