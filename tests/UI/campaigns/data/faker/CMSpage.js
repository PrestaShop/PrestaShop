const faker = require('faker');

/**
 * Create new cms page to use on creation cms page form on BO
 * @class
 */
class CMSPageData {
  /**
   * Constructor for class CMSPageData
   * @param pageToCreate {Object} Could be used to force the value of some members
   */
  constructor(pageToCreate = {}) {
    /** @member {string} Title of the page */
    this.title = pageToCreate.title || faker.random.word();

    /** @member {string} Meta title of the page */
    this.metaTitle = pageToCreate.metaTitle || faker.name.title();

    /** @member {string} Meta description for the page */
    this.metaDescription = faker.lorem.sentence();

    /** @member {string} Meta keyword for the page */
    this.metaKeywords = faker.lorem.word();

    /** @member {string} Content of the page */
    this.content = faker.lorem.sentence();

    /** @member {boolean} True to display the page on FO */
    this.displayed = pageToCreate.displayed === undefined ? true : pageToCreate.displayed;

    /** @member {boolean} True to index the page */
    this.indexation = pageToCreate.indexation === undefined ? true : pageToCreate.indexation;
  }
}

module.exports = CMSPageData;
