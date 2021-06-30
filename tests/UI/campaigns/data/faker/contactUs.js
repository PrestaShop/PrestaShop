const faker = require('faker');

const subject = ['Customer service', 'Webmaster'];

/**
 * Create new message to use on contact us form page on FO
 * @class
 */
class MessageData {
  /**
   * Constructor for class MessageData
   * @param messageToCreate {Object} Could be used to force the value of some members
   */
  constructor(messageToCreate = {}) {
    /** @member {string} Subject of the message */
    this.subject = messageToCreate.subject || faker.random.arrayElement(subject);

    /** @member {string} Firstname of the customer */
    this.firstName = messageToCreate.firstName || faker.name.firstName();

    /** @member {string} Firstname of the customer */
    this.lastName = messageToCreate.lastName || faker.name.lastName();

    /** @member {string} Email of the customer */
    this.emailAddress = messageToCreate.emailAddress
      || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @member {string} Reference for on order if used */
    this.reference = messageToCreate.reference;

    /** @member {string} Attachment name to add to the message */
    this.fileName = faker.lorem.word();

    /** @member {string} Content of the message */
    this.message = faker.lorem.sentence().substring(0, 35).trim();
  }
}

module.exports = MessageData;
