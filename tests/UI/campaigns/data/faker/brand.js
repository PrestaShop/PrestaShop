const faker = require('faker');

/**
 * Create new brand to use in brand form on BO
 * @class
 */
class BrandData {
  /**
   * Constructor for class BrandData
   * @param brandToCreate {Object} Could be used to force the value of some members
   */
  constructor(brandToCreate = {}) {
    /** @member {string} Name of the brand */
    this.name = brandToCreate.name || faker.company.companyName();

    /** @member {string} Logo name of the brand */
    this.logo = `${this.name.replace(/[^\w\s]/gi, '')}.png`;

    /** @member {string} Short description of the brand */
    this.shortDescription = brandToCreate.shortDescription || faker.lorem.sentence();

    /** @member {string} French short description of the brand */
    this.shortDescriptionFr = brandToCreate.shortDescriptionFr || this.shortDescription;

    /** @member {string} Description of the brand */
    this.description = brandToCreate.description || faker.lorem.sentence();

    /** @member {string} French description of the brand */
    this.descriptionFr = brandToCreate.descriptionFr || this.description;

    /** @member {string} Meta title of the brand */
    this.metaTitle = brandToCreate.metaTitle || this.name;

    /** @member {string} French meta title of the brand */
    this.metaTitleFr = brandToCreate.metaTitleFr || this.metaTitle;

    /** @member {string} Meta description of the brand */
    this.metaDescription = brandToCreate.metaDescription || faker.lorem.sentence();

    /** @member {string} French meta description of the brand */
    this.metaDescriptionFr = brandToCreate.metaDescriptionFr || this.metaDescription;

    /** @member {string} Meta Keywords of the brand */
    this.metaKeywords = brandToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];

    /** @member {string} French meta keywords of the brand */
    this.metaKeywordsFr = brandToCreate.metaKeywordsFr || this.metaKeywords;

    /** @member {string} Status of the brand */
    this.enabled = brandToCreate.enabled === undefined ? true : brandToCreate.enabled;

    /** @member {number} How much addresses has the brand */
    this.addresses = brandToCreate.addresses || 0;

    /** @member {number} How much products has the brand */
    this.products = brandToCreate.products || 0;
  }
}

module.exports = BrandData;
