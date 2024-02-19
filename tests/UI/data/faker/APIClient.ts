import type APIClientCreator from '@data/types/APIClient';

import {faker} from '@faker-js/faker';

/**
 * Create new API Client
 * @class
 */
export default class APIClientData {
  public readonly id: number;

  public readonly clientName: string;

  public readonly clientId: string;

  public readonly description: string;

  public readonly tokenLifetime: number;

  public readonly enabled: boolean;

  public readonly scopes: string[];

  /**
   * Constructor for class APIClientData
   * @param apiClientToCreate {APIClientCreator} Could be used to force the value of some members
   */
  constructor(apiClientToCreate: APIClientCreator = {}) {
    /** @type {string} Id of the API Client */
    this.id = apiClientToCreate.id || 0;

    /** @type {string} API Client Name */
    this.clientName = apiClientToCreate.clientName || faker.word.noun();

    /** @type {string} API Client ID */
    this.clientId = apiClientToCreate.clientId || faker.string.uuid();

    /** @type {string} Description */
    this.description = apiClientToCreate.description || faker.lorem.sentence();

    /** @type {string} Token Lifetime */
    this.tokenLifetime = apiClientToCreate.tokenLifetime || faker.number.int({min: 120, max: 3600});

    /** @type {boolean} Enabled */
    this.enabled = apiClientToCreate.enabled || faker.datatype.boolean();

    /** @type {string[]} Scopes */
    this.scopes = apiClientToCreate.scopes || [];
  }
}
