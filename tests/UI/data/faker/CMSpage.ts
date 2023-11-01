import CmsPageCreator from '@data/types/cmsPage';

import {faker} from '@faker-js/faker';

/**
 * Create new cms page to use on creation cms page form on BO
 * @class
 */
export default class CMSPageData {
  public readonly id: number;

  public readonly title: string;

  public readonly metaTitle: string;

  public readonly metaDescription: string;

  public readonly metaKeywords: string;

  public readonly content: string;

  public readonly url: string;

  public readonly displayed: boolean;

  public readonly position: number;

  public readonly indexation: boolean;

  /**
   * Constructor for class CMSPageData
   * @param pageToCreate {CmsPageCreator} Could be used to force the value of some members
   */
  constructor(pageToCreate: CmsPageCreator = {}) {
    /** @type {number} ID of the page */
    this.id = pageToCreate.id || 0;

    /** @type {string} Title of the page */
    this.title = pageToCreate.title || faker.lorem.word();

    /** @type {string} Meta title of the page */
    this.metaTitle = pageToCreate.metaTitle || faker.lorem.word();

    /** @type {string} Meta description for the page */
    this.metaDescription = pageToCreate.metaDescription || faker.lorem.sentence();

    /** @type {string} Meta keyword for the page */
    this.metaKeywords = pageToCreate.metaKeywords || faker.lorem.word();

    /** @type {string} Content of the page */
    this.content = pageToCreate.content || faker.lorem.sentence();

    /** @type {string} Meta title of the page */
    this.url = pageToCreate.url || '';

    /** @type {boolean} True to display the page on FO */
    this.displayed = pageToCreate.displayed === undefined ? true : pageToCreate.displayed;

    /** @type {number} Position */
    this.position = pageToCreate.position === undefined ? 0 : pageToCreate.position;

    /** @type {boolean} True to index the page */
    this.indexation = pageToCreate.indexation === undefined ? true : pageToCreate.indexation;
  }
}
