const faker = require('faker');

const {Features} = require('@data/demo/features');

const featuresNames = Object.values(Features).map(feature => feature.name);

module.exports = {
  Feature: class Attribute {
    constructor(featureToCreate = {}) {
      this.name = featureToCreate.name || faker.lorem.word();
      this.url = featureToCreate.url || this.name.replace(/\s/gi, '-');
      this.metaTitle = featureToCreate.metaTitle || faker.lorem.word();
      this.indexable = featureToCreate.indexable === undefined ? true : featureToCreate.indexable;
    }
  },
  Value: class Value {
    constructor(valueToCreate = {}) {
      this.featureName = valueToCreate.featureName || faker.random.arrayElement(featuresNames);
      this.value = valueToCreate.value || `${faker.lorem.word()}${faker.commerce.productMaterial()}`;
      this.url = valueToCreate.url || this.value.replace(/\s/gi, '-');
      this.metaTitle = valueToCreate.metaTitle || faker.lorem.word();
    }
  },
};
