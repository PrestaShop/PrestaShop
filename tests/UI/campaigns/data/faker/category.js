const faker = require('faker');

const {groupAccess} = require('@data/demo/groupAccess');

module.exports = class Category {
  constructor(categoryToCreate = {}) {
    this.name = categoryToCreate.name || `${faker.commerce.color()} ${faker.commerce.department()}`;
    this.displayed = categoryToCreate.displayed === undefined ? true : categoryToCreate.displayed;
    this.description = faker.lorem.sentence();
    this.metaTitle = categoryToCreate.metaTitle || faker.name.title();
    this.metaDescription = faker.lorem.sentence();
    this.groupAccess = categoryToCreate.groupAccess
      || faker.random.arrayElement(groupAccess);
  }
};
