import type APIAccessCreator from '@data/types/APIAccess';

import {faker} from '@faker-js/faker';

/**
 * Create new API Access
 * @class
 */
export default class APIAccessData {
  public readonly id: number;

  public readonly clientName: string;

  public readonly clientId: string;

  public readonly description: string;

  public readonly tokenLifetime: number;

  public readonly enabled: boolean;

  public readonly scopes: string[];

  /**
   * Constructor for class APIAccessData
   * @param apiAccessToCreate {APIAccessCreator} Could be used to force the value of some members
   */
  constructor(apiAccessToCreate: APIAccessCreator = {}) {
    /** @type {string} Id of the API Access */
    this.id = apiAccessToCreate.id || 0;

    /** @type {string} API Access Name */
    this.clientName = apiAccessToCreate.clientName || faker.word.noun();

    /** @type {string} API Access ID */
    this.clientId = apiAccessToCreate.clientId || faker.string.uuid();

    /** @type {string} Description */
    this.description = apiAccessToCreate.description || faker.lorem.sentence();

    /** @type {string} Token Lifetime */
    this.tokenLifetime = apiAccessToCreate.tokenLifetime || faker.number.int({min: 120, max: 3600});

    /** @type {boolean} Enabled */
    this.enabled = apiAccessToCreate.enabled || faker.datatype.boolean();

    /** @type {string[]} Scopes */
    this.scopes = apiAccessToCreate.scopes || [];
  }
}
