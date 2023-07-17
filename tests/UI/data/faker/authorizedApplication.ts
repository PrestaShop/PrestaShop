import type AuthorizedApplicationCreator from '@data/types/authorizedApplication';

import {faker} from '@faker-js/faker';

/**
 * Create new authorized Application
 * @class
 */
export default class AuthorizedApplicationData {
  public readonly id: number;

  public readonly appName: string;

  public readonly description: string;

  /**
   * Constructor for class AuthorizedApplicationData
   * @param authorizedApplicationToCreate {AuthorizedApplicationCreator} Could be used to force the value of some members
   */
  constructor(authorizedApplicationToCreate: AuthorizedApplicationCreator = {}) {
    /** @type {string} Id of the application */
    this.id = authorizedApplicationToCreate.id || 0;

    /** @type {string} Address Name */
    this.appName = authorizedApplicationToCreate.appName || faker.word.noun();

    /** @type {string} Customer firstname */
    this.description = authorizedApplicationToCreate.description || faker.lorem.sentence();
  }
}
