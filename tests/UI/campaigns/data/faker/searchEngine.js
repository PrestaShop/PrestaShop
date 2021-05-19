const faker = require('faker');

/**
 * Class representing search engine data
 * @class
 */
class SearchEngine {
  constructor(searchEngineToCreate = {}) {
    this.server = searchEngineToCreate.server || `test_${faker.internet.domainWord()}`;
    this.getVar = searchEngineToCreate.getVar || 'qTest_';
  }
}
module.exports = SearchEngine;
