const {faker} = require('@faker-js/faker');

/**
 * Create new feature to use on feature form on BO
 * @class
 */
class FeatureData {
  /**
   * Constructor for class FeatureData
   * @param featureToCreate {Object} Could be used to force the value of some members
   */
  constructor(featureToCreate = {}) {
    /** @type {string} Name of the feature */
    this.name = featureToCreate.name || faker.lorem.word();

    /** @type {string} Name used on the feature URL */
    this.url = featureToCreate.url || this.name.replace(/\s/gi, '-');

    /** @type {string} Feature meta title */
    this.metaTitle = featureToCreate.metaTitle || faker.lorem.word();

    /** @type {boolean} True for the feature to be indexed */
    this.indexable = featureToCreate.indexable === undefined ? true : featureToCreate.indexable;
  }
}

module.exports = FeatureData;
