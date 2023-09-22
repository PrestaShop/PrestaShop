// Import data
import TaxCreator from '@data/types/tax';

import {faker} from '@faker-js/faker';

/**
 * Create new tax to use on tax form on BO
 * @class
 */
export default class TaxData {
  public readonly id: number;

  public readonly rate: string;

  public readonly name: string;

  public readonly frName: string;

  public readonly enabled: boolean;

  /**
   * Constructor for class TaxData
   * @param taxToCreate {TaxCreator} Could be used to force the value of some members
   */
  constructor(taxToCreate: TaxCreator = {}) {
    /** @type {number} Name of the tax */
    this.id = taxToCreate.id || 0;

    /** @type {string} Tax of the rate */
    this.rate = taxToCreate.rate || faker.number.int({min: 1, max: 40}).toString();

    /** @type {string} Name of the tax */
    this.name = taxToCreate.name || `TVA test ${this.rate}%`;

    /** @type {string} French name of the tax */
    this.frName = taxToCreate.frName || this.name;

    /** @type {boolean} Status of the tax */
    this.enabled = taxToCreate.enabled === undefined ? true : taxToCreate.enabled;
  }
}
