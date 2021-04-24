const faker = require('faker');

module.exports = class Profile {
  constructor(aliasToCreate = {}) {
    this.alias = aliasToCreate.alias || `alias_${faker.lorem.word()}`;
    this.result = aliasToCreate.result || `result_${faker.lorem.word()}`;
  }
};
