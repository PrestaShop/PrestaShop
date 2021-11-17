const faker = require('faker');

/**
 * Create new search engine to use on search engine creation form on BO
 * @class
 */
class SearchEngineData {
  /**
   * Constructor for class SearchEngineData
   * @param searchEngineToCreate {Object} Could be used to force the value of some members
   */
  constructor(searchEngineToCreate = {}) {
    /** @type {string} Server of the engine */
    this.server = searchEngineToCreate.server || `test_${faker.internet.domainWord()}`;

    /** @type {string} Key to use on the search */
    this.queryKey = searchEngineToCreate.queryKey || 'qTest_';
  }
}

module.exports = SearchEngineData;
