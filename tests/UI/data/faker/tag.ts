import TagCreator from '@data/types/tag';

import {faker} from '@faker-js/faker';
import {
  dataLanguages,
  dataProducts,
  type FakerLanguage,
  type FakerProduct,
} from '@prestashop-core/ui-testing';

const productsNames: string[] = Object.values(dataProducts).map((product: FakerProduct) => product.name);
const languagesNames: string[] = Object.values(dataLanguages).map((language: FakerLanguage) => language.name);

/**
 * Create new tag to use on tag form on BO
 * @class
 */
export default class TagData {
  public name: string;

  public language: string;

  public products: string;

  /**
   * Constructor for class TagData
   * @param tagsToCreate {TagCreator} Could be used to force the value of some members
   */
  constructor(tagsToCreate: TagCreator = {}) {
    /** @type {string} Name of the tag */
    this.name = tagsToCreate.name || `new_tag_${faker.lorem.word()}`;

    /** @type {string} Language in which the tag should be used */
    this.language = tagsToCreate.language || faker.helpers.arrayElement(languagesNames);

    /** @type {string} Products linked to the tag */
    this.products = tagsToCreate.products || faker.helpers.arrayElement(productsNames);
  }
}
