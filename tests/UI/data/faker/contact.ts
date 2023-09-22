import ContactCreator from '@data/types/contact';

import {faker} from '@faker-js/faker';

/**
 * Create new contact to use on creation contact form on BO
 * @class
 */
export default class ContactData {
  public readonly id: number;

  public readonly firstName: string;

  public readonly lastName: string;

  public readonly title: string;

  public readonly email: string;

  public readonly saveMessage: boolean;

  public readonly description: string;

  /**
   * Constructor for class ContactData
   * @param contactToCreate {ContactCreator} Could be used to force the value of some members
   */
  constructor(contactToCreate: ContactCreator = {}) {
    /** @type {number} ID of the contact */
    this.id = contactToCreate.id || 0;

    /** @type {string} Firstname of the contact */
    this.firstName = contactToCreate.firstName || faker.person.firstName();

    /** @type {string} Lastname of the contact */
    this.lastName = contactToCreate.lastName || faker.person.lastName();

    /** @type {string} Title of the contact */
    this.title = contactToCreate.title || `${this.firstName} ${this.lastName}`;

    /** @type {string} Email of the contact */
    this.email = contactToCreate.email || faker.internet.email(
      {
        firstName: this.firstName,
        lastName: this.lastName,
        provider: 'prestashop.com',
      },
    );

    /** @type {boolean} True to save messages sent for the contact */
    this.saveMessage = contactToCreate.saveMessage === undefined ? true : contactToCreate.saveMessage;

    /** @type {string} Description of the contact */
    this.description = contactToCreate.description || faker.lorem.sentence();
  }
}
