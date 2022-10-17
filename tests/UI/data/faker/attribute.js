const {faker} = require('@faker-js/faker');

const attributeTypes = ['Drop-down list', 'Radio buttons', 'Color or texture'];

/**
 * Create new attribute to use on attribute form on BO
 * @class
 */
class AttributeData {
  /**
   * Constructor for class AttributeData
   * @param attributeToCreate {Object} Could be used to force the value of some members
   */
  constructor(attributeToCreate = {}) {
    /** @type {string} Name of the attribute */
    this.name = attributeToCreate.name || `${faker.lorem.word()}${faker.commerce.productMaterial()}`;

    /** @type {string} Public name of the attribute */
    this.publicName = attributeToCreate.publicName || this.name;

    /** @type {string} Name used on the attribute URL */
    this.url = attributeToCreate.url || this.name.replace(/\s/gi, '-');

    /** @type {string} Attribute meta title */
    this.metaTitle = attributeToCreate.metaTitle || faker.lorem.word();

    /** @type {boolean} True for the attribute to be indexed */
    this.indexable = attributeToCreate.indexable === undefined ? true : attributeToCreate.indexable;

    /** @type {string} Type of the attribute 'Drop-down list'/'Radio buttons'/'Color or texture' */
    this.attributeType = attributeToCreate.attributeType || faker.helpers.arrayElement(attributeTypes);
  }
}

module.exports = AttributeData;
