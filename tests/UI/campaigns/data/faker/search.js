const faker = require('faker');

/**
 * Class representing alias data
 * @class
 */
class Alias {
  constructor(aliasToCreate = {}) {
    this.alias = aliasToCreate.alias || `alias_${faker.lorem.word()}`;
    this.result = aliasToCreate.result || `result_${faker.lorem.word()}`;
  }
}
module.exports = Alias;
