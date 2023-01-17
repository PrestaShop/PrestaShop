import type FeatureValueData from '@data/faker/featureValue';
import {FeatureCreator} from '@data/types/feature';

import {faker} from '@faker-js/faker';

/**
 * Create new feature to use on feature form on BO
 * @class
 */
export default class FeatureData {
  public readonly id: number;

  public readonly position: number;

  public readonly url: string;

  public readonly name: string;

  public readonly metaTitle: string;

  public readonly indexable: boolean;

  public readonly values: FeatureValueData[];

  /**
   * Constructor for class FeatureData
   * @param featureToCreate {FeatureCreator} Could be used to force the value of some members
   */
  constructor(featureToCreate: FeatureCreator = {}) {
    /** @type {number} ID of the feature */
    this.id = featureToCreate.id || 0;

    /** @type {number} Position of the feature */
    this.position = featureToCreate.position || 0;

    /** @type {string} Name of the feature */
    this.name = featureToCreate.name || faker.lorem.word();

    /** @type {string} Name used on the feature URL */
    this.url = featureToCreate.url || this.name.replace(/\s/gi, '-');

    /** @type {string} Feature meta title */
    this.metaTitle = featureToCreate.metaTitle || faker.lorem.word();

    /** @type {boolean} True for the feature to be indexed */
    this.indexable = featureToCreate.indexable === undefined ? true : featureToCreate.indexable;

    /** @type {FeatureValueData[]}  */
    this.values = featureToCreate.values || [];
  }
}
