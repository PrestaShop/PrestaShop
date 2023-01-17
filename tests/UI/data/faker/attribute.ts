import AttributeValueData from '@data/faker/attributeValue';
import {AttributeCreator} from '@data/types/attribute';

import {faker} from '@faker-js/faker';

const attributeTypes: string[] = ['Drop-down list', 'Radio buttons', 'Color or texture'];

/**
 * Create new attribute to use on attribute form on BO
 * @class
 */
export default class AttributeData {
  public readonly id: number;

  public readonly position: number;

  public readonly name: string;

  public readonly publicName: string;

  public readonly url: string;

  public readonly metaTitle: string;

  public readonly indexable: boolean;

  public readonly displayed: boolean;

  public readonly attributeType: string;

  public readonly values: AttributeValueData[];

  /**
   * Constructor for class AttributeData
   * @param attributeToCreate {Object} Could be used to force the value of some members
   */
  constructor(attributeToCreate: AttributeCreator = {}) {
    /** @type {number} ID of the attribute */
    this.id = attributeToCreate.id || 0;

    /** @type {number} Position of the attribute */
    this.position = attributeToCreate.position || 0;

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

    /** @type {boolean} True for the attribute to be displayed */
    this.displayed = attributeToCreate.displayed === undefined ? true : attributeToCreate.displayed;

    /** @type {string} Type of the attribute 'Drop-down list'/'Radio buttons'/'Color or texture' */
    this.attributeType = attributeToCreate.attributeType || faker.helpers.arrayElement(attributeTypes);

    /** @type {AttributeValueData[]}  */
    this.values = attributeToCreate.values || [];
  }
}
