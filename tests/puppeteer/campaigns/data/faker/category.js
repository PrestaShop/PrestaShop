const faker = require('faker');

const groupAccess = ['Visitor', 'Guest', 'Customer'];

module.exports = class Category {
  constructor(categoryToCreate = {}) {
    this.name = categoryToCreate.name || faker.commerce.department();
    this.displayed = categoryToCreate.displayed || 'Yes';
    this.description = faker.lorem.sentence();
    this.coverImage = categoryToCreate.coverImage || faker.image.fashion();
    this.thunbnails = categoryToCreate.thunbnails || faker.image.avatar();
    this.menuThunbnail = categoryToCreate.menuThunbnail || faker.image.image();
    this.metaTitle = categoryToCreate.metaTitle || faker.name.title();
    this.metaDescription = faker.lorem.sentence();
    this.groupAccess = categoryToCreate.groupAccess
      || faker.random.arrayElement(groupAccess);
  }
};
