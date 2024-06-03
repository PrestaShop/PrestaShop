import type SqlQueryCreator from '@data/types/sqlQuery';

import {faker} from '@faker-js/faker';
import {
  dataSqlTables,
  type FakerSqlTable,
} from '@prestashop-core/ui-testing';

const tableNames: string[] = Object.values(dataSqlTables).map((table: FakerSqlTable) => table.name);

/**
 * Create new sql query to use on query creation form on BO
 * @class
 */
export default class SqlQueryData {
  public readonly name: string;

  public readonly tableName: string;

  public sqlQuery: string;

  /**
   * Constructor for class SqlQueryData
   * @param sqlQueryToCreate {SqlQueryCreator} Could be used to force the value of some members
   */
  constructor(sqlQueryToCreate: SqlQueryCreator = {}) {
    /** @type {string} Name of the query */
    this.name = sqlQueryToCreate.name || faker.lorem.word();

    /** @type {string} Table to use on the query */
    this.tableName = sqlQueryToCreate.tableName || faker.helpers.arrayElement(tableNames);

    /** @type {string} Value of the query */
    this.sqlQuery = sqlQueryToCreate.sqlQuery || `select * from ${this.tableName}`;
  }
}
