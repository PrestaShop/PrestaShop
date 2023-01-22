// Import data
import Languages from '@data/demo/languages';
import Products from '@data/demo/products';
import type LanguageData from '@data/faker/language';
import type ProductData from '@data/faker/product';
import TagCreator from '@data/types/tag';

import {faker} from '@faker-js/faker';

const productsNames: string[] = Object.values(Products).map((product: ProductData) => product.name);
const languagesNames: string[] = Object.values(Languages).map((language: LanguageData) => language.name);

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
