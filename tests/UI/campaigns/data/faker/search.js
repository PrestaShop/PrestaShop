const faker = require('faker');

/**
 * Create new alias to use on alias creation form on search page on BO
 * @class
 */
class SearchAliasData {
  /**
   * Constructor for class SearchAliasData
   * @param aliasToCreate {Object} Could be used to force the value of some members
   */
  constructor(aliasToCreate = {}) {
    /** @type {string} Name of the alias */
    this.alias = aliasToCreate.alias || `alias_${faker.lorem.word()}`;

    /** @type {string} Result to display on the search */
    this.result = aliasToCreate.result || `result_${faker.lorem.word()}`;
  }
}

module.exports = SearchAliasData;
