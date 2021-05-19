const faker = require('faker');

module.exports = class Contact {
  constructor(contactToCreate = {}) {
    this.firstName = contactToCreate.firstName || faker.name.firstName();
    this.lastName = contactToCreate.lastName || faker.name.lastName();
    this.saveMessage = contactToCreate.saveMessage === undefined ? true : contactToCreate.saveMessage;
    this.description = contactToCreate.description || faker.lorem.sentence();
  }
};
