const faker = require('faker');

module.exports = class PageCategory {
  constructor(pageCategoryToCreate = {}) {
    this.name = pageCategoryToCreate.name || faker.commerce.department();
    this.displayed = pageCategoryToCreate.displayed === undefined ? true : pageCategoryToCreate.displayed;
    this.description = faker.lorem.sentence();
    this.metaTitle = pageCategoryToCreate.metaTitle || faker.name.title();
    this.metaDescription = faker.lorem.sentence();
    this.metaKeywords = faker.commerce.department();
  }
};
