import type {CategoryCreator, CategoryRedirection} from '@data/types/category';

import {
  // Import data
  dataGroups,
  type FakerGroup,
} from '@prestashop-core/ui-testing';

import {faker} from '@faker-js/faker';

/**
 * Create new category to use on creation category form on BO
 * @class
 */
export default class CategoryData {
  public readonly id: number;

  public readonly position: number;

  public readonly name: string;

  public displayed: boolean;

  public readonly description: string;

  public readonly metaTitle: string;

  public readonly metaDescription: string;

  public readonly groupAccess: FakerGroup;

  public readonly coverImage: string | null;

  public readonly thumbnailImage: string | null;

  public readonly children: CategoryData[];

  public readonly products: string[];

  public redirectionWhenNotDisplayed: CategoryRedirection;

  public redirectedCategory: CategoryData | null;

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

    /** @type {FakerGroup} Customer group that could access to the category */
    this.groupAccess = categoryToCreate.groupAccess
      || faker.helpers.arrayElement([dataGroups.customer, dataGroups.guest, dataGroups.visitor]);

    /** @type {string|null} Category cover image of the category */
    this.coverImage = categoryToCreate.coverImage || null;

    /** @type {string|null} Category thumbnail of the category */
    this.thumbnailImage = categoryToCreate.thumbnailImage || null;

    /** @type {CategoryData[]} Category thumbnail of the category */
    this.children = categoryToCreate.children || [];

    /** @type {string[]} Products of the category */
    this.products = categoryToCreate.products || [];

    /** @type {CategoryRedirection} Redirection when not displayed */
    this.redirectionWhenNotDisplayed = categoryToCreate.redirectionWhenNotDisplayed || '301';

    /** @type {CategoryData|null} Which category should the page redirect? */
    this.redirectedCategory = categoryToCreate.redirectedCategory || null;
  }

  /**
   * @param {CategoryRedirection} redirection
   */
  setRedirectionWhenNotDisplayed(redirection: CategoryRedirection): this {
    this.redirectionWhenNotDisplayed = redirection;

    return this;
  }

  /**
   * @param {bool} displayed
   */
  setDisplayed(displayed: boolean): this {
    this.displayed = displayed;

    return this;
  }
}
