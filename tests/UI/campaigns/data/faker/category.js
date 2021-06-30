const faker = require('faker');

const {groupAccess} = require('@data/demo/groupAccess');

/**
 * Create new category to use on creation category form on BO
 * @class
 */
class CategoryData {
  /**
   * Constructor for class CategoryData
   * @param categoryToCreate {Object} Could be used to force the value of some members
   */
  constructor(categoryToCreate = {}) {
    /** @type {string} Name of the category */
    this.name = categoryToCreate.name || `${faker.commerce.color()} ${faker.commerce.department()}`;

    /** @type {boolean} True to display the category on FO */
    this.displayed = categoryToCreate.displayed === undefined ? true : categoryToCreate.displayed;

    /** @type {string} Description of the category */
    this.description = faker.lorem.sentence();

    /** @type {string} Meta title of the category */
    this.metaTitle = categoryToCreate.metaTitle || faker.name.title();

    /** @type {string} Meta description of the category */
    this.metaDescription = faker.lorem.sentence();

    /** @type {string} Customer group that could access to the category */
    this.groupAccess = categoryToCreate.groupAccess
      || faker.random.arrayElement(groupAccess);
  }
}

module.exports = CategoryData;
