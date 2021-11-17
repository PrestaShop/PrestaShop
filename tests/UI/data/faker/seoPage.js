const faker = require('faker');

/**
 * Create new Seo page to use on seo page creation form on BO
 * @class
 */
class SeoPageData {
  /**
   * Constructor for class SeoPageData
   * @param seoPageToCreate {Object} Could be used to force the value of some members
   */
  constructor(seoPageToCreate) {
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

    /** @type {Array<string>} Meta keywords of the page */
    this.metaKeywords = seoPageToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];

    /** @type {Array<string>} French meta keywords of the page */
    this.frMetaKeywords = seoPageToCreate.frMetaKeywords || this.metaKeywords;

    /** @type {string} Friendly url to display when accessing the page */
    this.friendlyUrl = seoPageToCreate.friendlyUrl || this.page.replace(new RegExp(' ', 'g'), '-');

    /** @type {string} French friendly url */
    this.frFriendlyUrl = seoPageToCreate.frFriendlyUrl || this.friendlyUrl;
  }
}

module.exports = SeoPageData;
