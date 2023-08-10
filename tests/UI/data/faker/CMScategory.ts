import CmsCategoryCreator from '@data/types/cmsCategory';

import {faker} from '@faker-js/faker';

/**
 * Create new cms category to use on creation cms category form on BO
 * @class
 */
export default class CMSCategoryData {
  public readonly name: string;

  public readonly displayed: boolean;

  public readonly description: string;

  public readonly metaTitle: string;

  public readonly metaDescription: string;

  /**
   * Constructor for class CMSCategoryData
   * @param categoryToCreate {CmsCategoryCreator} Could be used to force the value of some members
   */
  constructor(categoryToCreate: CmsCategoryCreator = {}) {
    /** @type {string} Name of the page category */
    this.name = categoryToCreate.name || faker.commerce.department();

    /** @type {boolean} True to display the category on FO */
    this.displayed = categoryToCreate.displayed === undefined ? true : categoryToCreate.displayed;

    /** @type {string} Description of the category */
    this.description = faker.lorem.sentence();

    /** @type {string} Meta title of the category */
    this.metaTitle = categoryToCreate.metaTitle || faker.lorem.word();

    /** @type {string} Meta description of the category */
    this.metaDescription = faker.lorem.sentence();
  }
}
