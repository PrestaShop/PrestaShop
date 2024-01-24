import Groups from '@data/demo/groups';
import type GroupData from '@data/faker/group';
import type {CategoryCreator} from '@data/types/category';

import {faker} from '@faker-js/faker';

/**
 * Create new category to use on creation category form on BO
 * @class
 */
export default class CategoryData {
  public readonly id: number;

  public readonly position: number;

  public readonly name: string;

  public readonly displayed: boolean;

  public readonly description: string;

  public readonly metaTitle: string;

  public readonly metaDescription: string;

  public readonly groupAccess: GroupData;

  public readonly coverImage: string | null;

  public readonly thumbnailImage: string | null;

  public readonly children: CategoryData[];

  public readonly products: string[];

  /**
   * Constructor for class CategoryData
   * @param categoryToCreate {CategoryCreator} Could be used to force the value of some members
   */
  constructor(categoryToCreate: CategoryCreator = {}) {
    /** @type {number} ID of the category */
    this.id = categoryToCreate.id || 0;

    /** @type {number} Position of the category */
    this.position = categoryToCreate.position || 0;

    /** @type {string} Name of the category */
    this.name = categoryToCreate.name || `${faker.color.human()} ${faker.commerce.department()}`;

    /** @type {boolean} True to display the category on FO */
    this.displayed = categoryToCreate.displayed === undefined ? true : categoryToCreate.displayed;

    /** @type {string} Description of the category */
    this.description = categoryToCreate.description || faker.lorem.sentence();

    /** @type {string} Meta title of the category */
    this.metaTitle = categoryToCreate.metaTitle || faker.lorem.word();

    /** @type {string} Meta description of the category */
    this.metaDescription = faker.lorem.sentence();

    /** @type {GroupData} Customer group that could access to the category */
    this.groupAccess = categoryToCreate.groupAccess
      || faker.helpers.arrayElement([Groups.customer, Groups.guest, Groups.visitor]);

    /** @type {string|null} Category cover image of the category */
    this.coverImage = categoryToCreate.coverImage || null;

    /** @type {string|null} Category thumbnail of the category */
    this.thumbnailImage = categoryToCreate.thumbnailImage || null;

    /** @type {CategoryData[]} Category thumbnail of the category */
    this.children = categoryToCreate.children || [];

    /** @type {string[]} Products of the category */
    this.products = categoryToCreate.products || [];
  }
}
