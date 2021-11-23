const faker = require('faker');
const {Products} = require('@data/demo/products');
const {Languages} = require('@data/demo/languages');

const productsNames = Object.values(Products).map(product => product.name);
const languagesNames = Object.values(Languages).map(language => language.name);

/**
 * Create new tag to use on tag form on BO
 * @class
 */
class TagData {
  /**
   * Constructor for class TagData
   * @param tagsToCreate {Object} Could be used to force the value of some members
   */
  constructor(tagsToCreate = {}) {
    /** @type {string} Name of the tag */
    this.name = tagsToCreate.name || `new_tag_${faker.lorem.word()}`;

    /** @type {string} Language in which the tag should be used */
    this.language = tagsToCreate.language || faker.random.arrayElement(languagesNames);

    /** @type {Array/string} Products linked to the tag */
    this.products = tagsToCreate.products || faker.random.arrayElement(productsNames);
  }
}

module.exports = TagData;
