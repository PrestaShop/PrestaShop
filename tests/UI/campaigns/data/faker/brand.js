const faker = require('faker');

module.exports = class Brand {
  constructor(brandToCreate = {}) {
    this.name = brandToCreate.name || faker.company.companyName();
    this.logo = `${this.name.replace(/[^\w\s]/gi, '')}.png`;
    this.shortDescription = brandToCreate.shortDescription || faker.lorem.sentence();
    this.shortDescriptionFr = brandToCreate.shortDescriptionFr || this.shortDescription;
    this.description = brandToCreate.description || faker.lorem.sentence();
    this.descriptionFr = brandToCreate.descriptionFr || this.description;
    this.metaTitle = brandToCreate.metaTitle || this.name;
    this.metaTitleFr = brandToCreate.metaTitleFr || this.metaTitle;
    this.metaDescription = brandToCreate.metaDescription || faker.lorem.sentence();
    this.metaDescriptionFr = brandToCreate.metaDescriptionFr || this.metaDescription;
    this.metaKeywords = brandToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];
    this.metaKeywordsFr = brandToCreate.metaKeywordsFr || this.metaKeywords;
    this.enabled = brandToCreate.enabled === undefined ? true : brandToCreate.enabled;
    this.addresses = brandToCreate.addresses || 0;
    this.products = brandToCreate.products || 0;
  }
};
