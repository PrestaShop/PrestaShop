import type {FeatureValueCreator} from '@data/types/feature';

import {faker} from '@faker-js/faker';

const featuresNames: string[] = ['Composition', 'Property'];

/**
 * Create new feature value to use on feature value form on BO
 * @class
 */
export default class FeatureValueData {
  public readonly id:number;

  public readonly featureName:string;

  public readonly value:string;

  public readonly url:string;

  public readonly metaTitle:string;

  /**
   * Constructor for class ValueData
   * @param valueToCreate {FeatureValueCreator} Could be used to force the value of some members
   */
  constructor(valueToCreate: FeatureValueCreator = {}) {
    /** @type {number} ID of the feature */
    this.id = valueToCreate.id || 0;

    /** @type {string} Name of the parent feature */
    this.featureName = valueToCreate.featureName || faker.helpers.arrayElement(featuresNames);

    /** @type {string} Name of the value */
    this.value = valueToCreate.value || `${faker.lorem.word()}${faker.commerce.productMaterial()}`;

    /** @type {string} Name used on the value URL */
    this.url = valueToCreate.url || this.value.replace(/\s/gi, '-');

    /** @type {string} Feature value meta title */
    this.metaTitle = valueToCreate.metaTitle || faker.lorem.word();
  }
}
