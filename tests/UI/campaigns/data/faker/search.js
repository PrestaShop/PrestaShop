const faker = require('faker');

module.exports = class Profile {
  constructor(aliasToCreate = {}) {
    this.alias = aliasToCreate.alias || `alias_${faker.lorem.word()}`;
    this.result = aliasToCreate.result || `alias_${faker.lorem.word()}`;
  }
};
