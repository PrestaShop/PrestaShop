const faker = require('faker');

module.exports = {
  Feature: class Attribute {
    constructor(featureToCreate = {}) {
      this.name = featureToCreate.name || faker.lorem.word();
      this.url = featureToCreate.url || this.name.replace(/\s/gi, '-');
      this.metaTitle = featureToCreate.metaTitle || faker.lorem.word();
      this.indexable = featureToCreate.indexable === undefined ? true : featureToCreate.indexable;
    }
  },
};
