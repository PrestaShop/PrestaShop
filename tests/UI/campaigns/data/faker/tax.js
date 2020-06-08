const faker = require('faker');

module.exports = class Tax {
  constructor(taxToCreate = {}) {
    this.rate = taxToCreate.rate || faker.random.number({min: 1, max: 40}).toString();
    this.name = taxToCreate.name || `TVA test ${this.rate}%`;
    this.frName = taxToCreate.frName || this.name;
    this.enabled = taxToCreate.enabled === undefined ? true : taxToCreate.enabled;
  }
};
