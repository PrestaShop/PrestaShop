const faker = require('faker');

/**
 * Create new order message to use on creation form on order message page on BO
 * @class
 */
class OrderMessageData {
  /**
   * Constructor for class OrderMessage
   * @param messageToCreate {Object} Could be used to force the value of some members
   */
  constructor(messageToCreate = {}) {
    /** @member {string} Name of the message */
    this.name = messageToCreate.name || faker.lorem.word();

    /** @member {string} The message to set */
    this.message = messageToCreate.message || faker.lorem.sentence();

    /** @member {string} French name of the message */
    this.frName = messageToCreate.frName || this.name;

    /** @member {string} The french message to set */
    this.frMessage = messageToCreate.frMessage || this.message;
  }
}
module.exports = OrderMessageData;
