import type ModuleDataCreator from '@data/types/module';

/**
 * @class
 */
export default class ModuleData {
  public readonly tag: string;

  public readonly name: string;

  public readonly releaseZip: string;

  /**
   * Constructor for class ModuleData
   * @param valueToCreate {ModuleDataCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: ModuleDataCreator = {}) {
    /** @type {string} Technical Name of the module */
    this.tag = valueToCreate.tag || '';

    /** @type {string} Name of the module */
    this.name = valueToCreate.name || '';

    /** @type {string} Release URL */
    this.releaseZip = valueToCreate.releaseZip || '';
  }
}
