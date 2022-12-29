// Import data
import ZoneCreator from '@data/types/zone';

import {faker} from '@faker-js/faker';

/**
 * Create new zone to use on zone form on BO
 * @class
 */
export default class ZoneData {
  public readonly id: number;

  public readonly name: string;

  public readonly status: boolean;

  /**
   * Constructor for class ZoneData
   * @param zoneToCreate {Object} Could be used to force the value of some members
   */
  constructor(zoneToCreate: ZoneCreator = {}) {
    /** @type {number} */
    this.id = zoneToCreate.id || 0;

    /** @type {string} Name of the zone */
    this.name = zoneToCreate.name || `test ${faker.lorem.word()}`;

    /** @type {boolean} Status of the zone */
    this.status = zoneToCreate.status === undefined ? true : zoneToCreate.status;
  }
}
