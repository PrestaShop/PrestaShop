import MessageCreator from '@data/types/message';

import {faker} from '@faker-js/faker';

const subject: string[] = ['Customer service', 'Webmaster'];

/**
 * Create new message to use on contact us form page on FO
 * @class
 */
export default class MessageData {
  public readonly subject: string;

  public readonly firstName: string;

  public readonly lastName: string;

  public readonly employeeName: string;

  public readonly emailAddress: string;

  public reference: string;

  public readonly fileName: string;

  public message: string;

  /**
   * Constructor for class MessageData
   * @param messageToCreate {MessageCreator} Could be used to force the value of some members
   */
  constructor(messageToCreate: MessageCreator = {}) {
    /** @type {string} Subject of the message */
    this.subject = messageToCreate.subject || faker.helpers.arrayElement(subject);

    /** @type {string} Firstname of the customer */
    this.firstName = messageToCreate.firstName || faker.person.firstName();

    /** @type {string} Firstname of the customer */
    this.lastName = messageToCreate.lastName || faker.person.lastName();

    /** @type {string} employee to forward the message */
    this.employeeName = messageToCreate.employeeName || `${this.firstName.slice(0.1)}. ${this.lastName}`;

    /** @type {string} Email of the customer */
    this.emailAddress = messageToCreate.emailAddress === undefined
      ? faker.internet.email({firstName: this.firstName, lastName: this.lastName, provider: 'prestashop.com'})
      : messageToCreate.emailAddress;

    /** @type {string} Reference for on order if used */
    this.reference = messageToCreate.reference || '';

    /** @type {string} Attachment name to add to the message */
    this.fileName = messageToCreate.fileName || faker.lorem.word();

    /** @type {string} Content of the message */
    this.message = messageToCreate.message === undefined
      ? faker.lorem.sentence().substring(0, 35).trim()
      : messageToCreate.message;
  }
}
