const {faker} = require('@faker-js/faker');

const {Attributes} = require('@data/demo/attributes');

const attributesNames = Object.values(Attributes).map((attribute) => attribute.name);

/**
 * Create new attribute value to use on attribute value form on BO
 * @class
 */
class AttributeValueData {
  /**
   * Constructor for class ValueData
   * @param valueToCreate {Object} Could be used to force the value of some members
   */
  constructor(valueToCreate = {}) {
    /** @type {string} Name of the parent attribute */
    this.attributeName = valueToCreate.attributeName || faker.helpers.arrayElement(attributesNames);

    /** @type {string} Name of the value */
    this.value = valueToCreate.value || `${faker.lorem.word()}${faker.commerce.productMaterial()}`;

    /** @type {string} Name used on the value URL */
    this.url = valueToCreate.url || this.value.replace(/\s/gi, '-');

    /** @type {string} Attribute value meta title */
    this.metaTitle = valueToCreate.metaTitle || faker.lorem.word();

    /** @type {string} if the attribute type is color, hexadecimal value of the color */
    this.color = valueToCreate.color || faker.internet.color();

    /** @type {string} if the attribute type is texture, filename of the texture */
    this.textureFileName = valueToCreate.textureFileName || faker.system.commonFileName('txt');
  }
}

module.exports = AttributeValueData;
