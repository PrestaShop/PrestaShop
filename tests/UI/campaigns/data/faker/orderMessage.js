const faker = require('faker');
/**
 * Class representing order message data
 * @class
 */
class OrderMessage {
  constructor(messageToCreate = {}) {
    this.name = messageToCreate.name || faker.lorem.word();
    this.message = messageToCreate.name || faker.lorem.sentence();
    this.frName = messageToCreate.frName || this.name;
    this.frMessage = messageToCreate.frName || this.message;
  }
}
module.exports = OrderMessage;
