const faker = require('faker');

module.exports = class OrderMessage {
  constructor(messageToCreate = {}) {
    this.name = messageToCreate.name || faker.lorem.word();
    this.message = messageToCreate.name || faker.lorem.sentence();
    this.frName = messageToCreate.frName || this.name;
    this.frMessage = messageToCreate.frName || this.message;
  }
};
