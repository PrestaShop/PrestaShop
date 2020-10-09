const faker = require('faker');

module.exports = class Page {
  constructor(pageToCreate = {}) {
    this.title = pageToCreate.title || faker.random.word();
    this.metaTitle = pageToCreate.metaTitle || faker.name.title();
    this.metaDescription = faker.lorem.sentence();
    this.metaKeywords = faker.lorem.word();
    this.content = faker.lorem.sentence();
    this.displayed = pageToCreate.displayed === undefined ? true : pageToCreate.displayed;
    this.indexation = pageToCreate.indexation === undefined ? true : pageToCreate.indexation;
  }
};
