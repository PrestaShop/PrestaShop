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
    /** @member {string} Page name from form list */
    this.page = seoPageToCreate.page;

    /** @member {string} Title of the page to add */
    this.title = seoPageToCreate.title || faker.lorem.word();

    /** @member {string} French title of the page to add */
    this.frTitle = seoPageToCreate.frTitle || this.title;

    /** @member {string} Meta description of the page */
    this.metaDescription = seoPageToCreate.metaDescription || faker.lorem.sentence();

    /** @member {string} French meta description of the page */
    this.frMetaDescription = seoPageToCreate.frMetaDescription || this.metaDescription;

    /** @member {Array<string>} Meta keywords of the page */
    this.metaKeywords = seoPageToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];

    /** @member {Array<string>} French meta keywords of the page */
    this.frMetaKeywords = seoPageToCreate.frMetaKeywords || this.metaKeywords;

    /** @member {string} Friendly url to display when accessing the page */
    this.friendlyUrl = seoPageToCreate.friendlyUrl || this.page.replace(new RegExp(' ', 'g'), '-');

    /** @member {string} French friendly url */
    this.frFriendlyUrl = seoPageToCreate.frFriendlyUrl || this.friendlyUrl;
  }
}

module.exports = SeoPageData;
