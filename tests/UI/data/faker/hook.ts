import type HookCreator from '@data/types/hook';

import {faker} from '@faker-js/faker';

/**
 * @class
 */
export default class HookData {
  public readonly id: number;

  public readonly name: string;

  /**
   * Constructor for class HookData
   * @param valueToCreate {HookCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: HookCreator = {}) {
    /** @type {number} */
    this.id = valueToCreate.id || 0;

    /** @type {string} Name of the currency */
    this.name = valueToCreate.name || faker.word.noun();
  }
}
