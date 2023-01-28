import type ModuleDataCreator from '@data/types/module';

/**
 * @class
 */
export default class ModuleData {
  public readonly tag: string;

  public readonly name: string;

  /**
   * Constructor for class HookData
   * @param valueToCreate {ModuleDataCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: ModuleDataCreator = {}) {
    /** @type {string} Name of the currency */
    this.tag = valueToCreate.tag || '';

    /** @type {string} Name of the currency */
    this.name = valueToCreate.name || '';
  }
}
