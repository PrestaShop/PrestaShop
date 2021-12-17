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
    /** @type {string} Title of the page */
    this.title = pageToCreate.title || faker.random.word();

    /** @type {string} Meta title of the page */
    this.metaTitle = pageToCreate.metaTitle || faker.name.title();

    /** @type {string} Meta description for the page */
    this.metaDescription = faker.lorem.sentence();

    /** @type {string} Meta keyword for the page */
    this.metaKeywords = faker.lorem.word();

    /** @type {string} Content of the page */
    this.content = faker.lorem.sentence();

    /** @type {boolean} True to display the page on FO */
    this.displayed = pageToCreate.displayed === undefined ? true : pageToCreate.displayed;

    /** @type {boolean} True to index the page */
    this.indexation = pageToCreate.indexation === undefined ? true : pageToCreate.indexation;
  }
}

module.exports = CMSPageData;
