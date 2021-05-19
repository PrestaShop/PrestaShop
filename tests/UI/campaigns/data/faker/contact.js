const faker = require('faker');

module.exports = class Contact {
  constructor(contactToCreate = {}) {
    this.firstName = contactToCreate.firstName || faker.name.firstName();
    this.lastName = contactToCreate.lastName || faker.name.lastName();
    this.title = contactToCreate.title || `${this.firstName} ${this.lastName}`;
    this.email = contactToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.saveMessage = contactToCreate.saveMessage === undefined ? true : contactToCreate.saveMessage;
    this.description = contactToCreate.description || faker.lorem.sentence();
  }
};
