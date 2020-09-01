const faker = require('faker');

module.exports = class Country {
  constructor(countryToCreate = {}) {
    this.firstName = countryToCreate.firstName || faker.name.firstName();
    this.lastName = countryToCreate.lastName || faker.name.lastName();
    this.title = countryToCreate.title || `${this.firstName} ${this.lastName}`;
    this.email = countryToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.saveMessage = countryToCreate.saveMessage === undefined ? true : countryToCreate.saveMessage;
    this.description = countryToCreate.description || faker.lorem.sentence();
  }
};
