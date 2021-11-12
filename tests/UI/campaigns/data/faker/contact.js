const faker = require('faker');

/**
 * Create new contact to use on creation contact form on BO
 * @class
 */
class ContactData {
  /**
   * Constructor for class ContactData
   * @param contactToCreate {Object} Could be used to force the value of some members
   */
  constructor(contactToCreate = {}) {
    /** @type {string} Firstname of the contact */
    this.firstName = contactToCreate.firstName || faker.name.firstName();

    /** @type {string} Lastname of the contact */
    this.lastName = contactToCreate.lastName || faker.name.lastName();

    /** @type {string} Title of the contact */
    this.title = contactToCreate.title || `${this.firstName} ${this.lastName}`;

    /** @type {string} Email of the contact */
    this.email = contactToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');

    /** @type {boolean} True to save messages sent for the contact */
    this.saveMessage = contactToCreate.saveMessage === undefined ? true : contactToCreate.saveMessage;

    /** @type {string} Description of the contact */
    this.description = contactToCreate.description || faker.lorem.sentence();
  }
}

module.exports = ContactData;
