// Import data
import {WebserviceCreator, WebservicePermission} from '@data/types/webservice';

import {faker} from '@faker-js/faker';

/**
 * Create new webservice to use on webservice form on BO
 * @class
 */
export default class WebserviceData {
  public readonly key: string;

  public readonly keyDescription: string;

  public readonly status: boolean;

  public readonly permissions: WebservicePermission[];

  /**
   * Constructor for class WebserviceData
   * @param webserviceToCreate {WebserviceCreator} Could be used to force the value of some members
   */
  constructor(webserviceToCreate: WebserviceCreator = {}) {
    /** @type {string} Key of the webservice */
    this.key = webserviceToCreate.key || faker.datatype.uuid().substring(0, 32);

    /** @type {string} Key description of the webservice */
    this.keyDescription = webserviceToCreate.keyDescription || faker.lorem.sentence();

    /** @type {boolean} Status of the webservice */
    this.status = webserviceToCreate.status === undefined ? true : webserviceToCreate.status;

    /** @type {WebservicePermission[]} Permissions of the webservice */
    this.permissions = webserviceToCreate.permissions === undefined ? [] : webserviceToCreate.permissions;
  }
}
