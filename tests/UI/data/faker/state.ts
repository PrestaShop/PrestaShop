import Zones from '@data/demo/zones';
import ZoneData from '@data/faker/zone';
import type StateCreator from '@data/types/state';

import {faker} from '@faker-js/faker';

const zones: string[] = Object.values(Zones).map((zone: ZoneData) => zone.name);
const countriesWithState: string[] = [
  'Argentina', 'Australia', 'Canada', 'India', 'Indonesia', 'Italy', 'Japan', 'Mexico', 'United States',
];
const statesIsoCodes: string[] = ['IR', 'PK', 'BP', 'BV', 'ZM', 'ZL', 'HM', 'HL', 'BK'];

/**
 * Create new state to use on state creation form on BO
 * @class
 */
export default class StateData {
  public readonly id: number;

  public readonly name: string;

  public readonly isoCode: string;

  public readonly country: string;

  public readonly zone: string;

  public readonly status: boolean;

  /**
   * Constructor for class StateData
   * @param stateToCreate {StateCreator} Could be used to force the value of some members
   */
  constructor(stateToCreate: StateCreator = {}) {
    /** @type {number} ID of the state */
    this.id = stateToCreate.id || 0;

    /** @type {string} Name of the state */
    this.name = stateToCreate.name || `test ${faker.location.state()}`;

    /** @type {string} Iso code of the state */
    this.isoCode = stateToCreate.isoCode || faker.helpers.arrayElement(statesIsoCodes);

    /** @type {string} Country of the state */
    this.country = stateToCreate.country || faker.helpers.arrayElement(countriesWithState);

    /** @type {string} Zone of the state */
    this.zone = stateToCreate.zone || faker.helpers.arrayElement(zones);

    /** @type {boolean} Status of the state */
    this.status = stateToCreate.status === undefined ? false : stateToCreate.status;
  }
}
