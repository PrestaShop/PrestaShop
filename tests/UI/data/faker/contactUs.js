const {faker} = require('@faker-js/faker');

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
    /** @type {string} Subject of the message */
    this.subject = messageToCreate.subject || faker.helpers.arrayElement(subject);

    /** @type {string} Firstname of the customer */
    this.firstName = messageToCreate.firstName || faker.name.firstName();

    /** @type {string} Firstname of the customer */
    this.lastName = messageToCreate.lastName || faker.name.lastName();

    /** @type {string} Email of the customer */
    this.emailAddress = messageToCreate.emailAddress === undefined
      ? faker.internet.email(this.firstName, this.lastName, 'prestashop.com')
      : messageToCreate.emailAddress;

    /** @type {string} Reference for on order if used */
    this.reference = messageToCreate.reference;

    /** @type {string} Attachment name to add to the message */
    this.fileName = faker.lorem.word();

    /** @type {string} Content of the message */
    this.message = messageToCreate.message === undefined
      ? faker.lorem.sentence().substring(0, 35).trim()
      : messageToCreate.message;
  }
}

module.exports = MessageData;
