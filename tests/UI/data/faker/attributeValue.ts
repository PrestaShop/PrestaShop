import {AttributeValueCreator} from '@data/types/attribute';

import {faker} from '@faker-js/faker';

const attributesNames: string[] = ['Size', 'Color', 'Dimension', 'Paper Type'];

/**
 * Create new attribute value to use on attribute value form on BO
 * @class
 */
export default class AttributeValueData {
  public readonly id: number;

  public readonly position: number;

  public readonly attributeName: string;

  public readonly value: string;

  public readonly url: string;

  public readonly metaTitle: string;

  public readonly color: string;

  public readonly textureFileName: string;

  /**
   * Constructor for class ValueData
   * @param valueToCreate {Object} Could be used to force the value of some members
   */
  constructor(valueToCreate: AttributeValueCreator = {}) {
    /** @type {number} ID */
    this.id = valueToCreate.id || 0;

    /** @type {number} Position */
    this.position = valueToCreate.position || 0;

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
