const {faker} = require('@faker-js/faker');

const {Features} = require('@data/demo/features');

const featuresNames = Object.values(Features).map((feature) => feature.name);

/**
 * Create new feature value to use on feature value form on BO
 * @class
 */
class FeatureValueData {
  /**
   * Constructor for class ValueData
   * @param valueToCreate {Object} Could be used to force the value of some members
   */
  constructor(valueToCreate = {}) {
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

module.exports = FeatureValueData;
