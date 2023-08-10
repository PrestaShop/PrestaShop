import type SeoPageCreator from '@data/types/seoPage';

import {faker} from '@faker-js/faker';

/**
 * Create new Seo page to use on seo page creation form on BO
 * @class
 */
export default class SeoPageData {
  public readonly id: number;

  public readonly page: string;

  public readonly title: string;

  public readonly frTitle: string;

  public readonly metaDescription: string;

  public readonly frMetaDescription: string;

  public readonly friendlyUrl: string;

  public readonly frFriendlyUrl: string;

  /**
   * Constructor for class SeoPageData
   * @param seoPageToCreate {SeoPageCreator} Could be used to force the value of some members
   */
  constructor(seoPageToCreate: SeoPageCreator) {
    /** @type {number} ID */
    this.id = seoPageToCreate.id || 0;

    /** @type {string} Page name from form list */
    this.page = seoPageToCreate.page;

    /** @type {string} Title of the page to add */
    this.title = seoPageToCreate.title || faker.lorem.word();

    /** @type {string} French title of the page to add */
    this.frTitle = seoPageToCreate.frTitle || this.title;

    /** @type {string} Meta description of the page */
    this.metaDescription = seoPageToCreate.metaDescription || faker.lorem.sentence();

    /** @type {string} French meta description of the page */
    this.frMetaDescription = seoPageToCreate.frMetaDescription || this.metaDescription;

    /** @type {string} Friendly url to display when accessing the page */
    this.friendlyUrl = seoPageToCreate.friendlyUrl || this.page.replace(/ /g, '-');

    /** @type {string} French friendly url */
    this.frFriendlyUrl = seoPageToCreate.frFriendlyUrl || this.friendlyUrl;
  }
}
