import type SqlTableCreator from '@data/types/sqlTable';

/**
 * Create new sql table
 * @class
 */
export default class SqlTableData {
  public readonly name: string;

  public readonly columns: string[];

  /**
   * Constructor for class SqlTableData
   * @param sqlTableToCreate {SqlTableCreator} Could be used to force the value of some members
   */
  constructor(sqlTableToCreate: SqlTableCreator = {}) {
    /** @type {string} Name of the table */
    this.name = sqlTableToCreate.name || '';

    /** @type {string[]} Columns of the query */
    this.columns = sqlTableToCreate.columns || [];
  }
}
