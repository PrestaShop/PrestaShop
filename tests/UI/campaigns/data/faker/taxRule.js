const faker = require('faker');

const {behaviour} = require('@data/demo/taxRule');
const {tax} = require('@data/demo/tax');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);

module.exports = class Tax {
  constructor(taxRulesToCreate = {}) {
    this.country = taxRulesToCreate.country || faker.random.arrayElement(countriesNames);
    this.zipCode = taxRulesToCreate.zipCode || faker.address.zipCode();
    this.behaviour = taxRulesToCreate.behaviour || faker.random.arrayElement(behaviour);
    this.tax = taxRulesToCreate.tax || tax.DefaultFrTax.name;
    this.description = taxRulesToCreate.description || faker.lorem.sentence();
  }
};
