const faker = require('faker');

const {Features} = require('@data/demo/features');

const featuresNames = Object.values(Features).map(feature => feature.name);

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

/**
 * Create new feature value to use on feature value form on BO
 * @class
 */
class ValueData {
  /**
   * Constructor for class ValueData
   * @param valueToCreate {Object} Could be used to force the value of some members
   */
  constructor(valueToCreate = {}) {
    /** @type {string} Name of the parent feature */
    this.featureName = valueToCreate.featureName || faker.random.arrayElement(featuresNames);

    /** @type {string} Name of the value */
    this.value = valueToCreate.value || `${faker.lorem.word()}${faker.commerce.productMaterial()}`;

    /** @type {string} Name used on the value URL */
    this.url = valueToCreate.url || this.value.replace(/\s/gi, '-');

    /** @type {string} Feature value meta title */
    this.metaTitle = valueToCreate.metaTitle || faker.lorem.word();
  }
}

module.exports = {FeatureData, ValueData};
