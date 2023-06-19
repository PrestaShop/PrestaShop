// Import data
import TaxRulesGroupCreator from '@data/types/taxRulesGroup';

import {faker} from '@faker-js/faker';

/**
 * Create new tax rules group to use on tax rules group form on BO
 * @class
 */
export default class TaxRulesGroupData {
  public readonly name: string;

  public readonly enabled: boolean;

  /**
   * Constructor for class TaxRulesGroupData
   * @param taxRulesGroupToCreate {Object} Could be used to force the value of some members
   */
  constructor(taxRulesGroupToCreate: TaxRulesGroupCreator = {}) {
    /** @type {string} Name of the tax rules group */
    this.name = (taxRulesGroupToCreate.name || `FR tax Rule ${faker.lorem.word()}`).substring(0, 30).trim();

    /** @type {boolean} Status of the tax rules group */
    this.enabled = taxRulesGroupToCreate.enabled === undefined ? true : taxRulesGroupToCreate.enabled;
  }
}
