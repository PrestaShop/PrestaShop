import OrderMessageCreator from '@data/types/orderMessage';

import {faker} from '@faker-js/faker';

/**
 * Create new order message to use on creation form on order message page on BO
 * @class
 */
export default class OrderMessageData {
  public readonly name: string;

  public readonly message: string;

  public readonly frName: string;

  public readonly frMessage: string;

  /**
   * Constructor for class OrderMessage
   * @param messageToCreate {OrderMessageCreator} Could be used to force the value of some members
   */
  constructor(messageToCreate: OrderMessageCreator = {}) {
    /** @type {string} Name of the message */
    this.name = messageToCreate.name || faker.lorem.word();

    /** @type {string} The message to set */
    this.message = messageToCreate.message || faker.lorem.sentence();

    /** @type {string} French name of the message */
    this.frName = messageToCreate.frName || this.name;

    /** @type {string} The french message to set */
    this.frMessage = messageToCreate.frMessage || this.message;
  }
}
