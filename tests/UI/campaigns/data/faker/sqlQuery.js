const faker = require('faker');
const {Tables} = require('@data/demo/sqlTables');

/**
 * Create new sql query to use on query creation form on BO
 * @class
 */
class SqlQueryData {
  /**
   * Constructor for class SqlQueryData
   * @param sqlQueryToCreate {Object} Could be used to force the value of some members
   */
  constructor(sqlQueryToCreate = {}) {
    /** @type {string} Name of the query */
    this.name = sqlQueryToCreate.name || faker.random.word();

    /** @type {string} Table to use on the query */
    this.tableName = sqlQueryToCreate.tableName
      || faker.random.arrayElement((Object.values(Tables).map(table => table.key)));

    /** @type {string} Value of the query */
    this.sqlQuery = sqlQueryToCreate.sqlQuery || `select * from ${this.tableName}`;
  }
}

module.exports = SqlQueryData;
