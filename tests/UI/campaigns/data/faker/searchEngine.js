const faker = require('faker');

module.exports = class SearchEngine {
  constructor(searchEngineToCreate = {}) {
    this.server = searchEngineToCreate.server || `test_${faker.internet.domainWord()}`;
    this.getVar = searchEngineToCreate.getVar || 'qTest_';
  }
};
