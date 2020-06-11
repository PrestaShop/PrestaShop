const faker = require('faker');

module.exports = class Tax {
  constructor(taxRulesGroupToCreate = {}) {
    this.name = taxRulesGroupToCreate.name || `FR tax Rule ${faker.random.word()}`;
    this.enabled = taxRulesGroupToCreate.enabled === undefined ? true : taxRulesGroupToCreate.enabled;
  }
};
