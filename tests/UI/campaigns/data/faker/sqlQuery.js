const faker = require('faker');
const {Tables} = require('@data/demo/sqlTables');

module.exports = class Tax {
  constructor(sqlQueryToCreate = {}) {
    this.name = sqlQueryToCreate.name || faker.random.word();
    this.tableName = sqlQueryToCreate.tableName
      || faker.random.arrayElement((Object.values(Tables).map(table => table.key)));
    this.sqlQuery = sqlQueryToCreate.sqlQuery
      || `select * from ${this.tableName}`;
  }
};
