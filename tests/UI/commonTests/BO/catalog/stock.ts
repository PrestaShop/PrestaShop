// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {createConnection} from 'mysql2/promise';
import type {Connection} from 'mysql2/promise';

let dbConnection: Connection;

/**
 * Function to clean all Stock Movements
 * @param baseContext {string} String to identify the test
 */
function cleanTableStockMovements(baseContext: string = 'commonTests-cleanTableStockMovements'): void {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;

  describe(`Clean table ${dbPrefix}stock_mvt`, async () => {
    // before and after functions
    before(async () => {
      if (!global.GENERATE_FAILED_STEPS) {
        dbConnection = await createConnection({
          user: global.INSTALL.DB_USER,
          password: global.INSTALL.DB_PASSWD,
          host: 'localhost',
          port: 3306,
          database: global.INSTALL.DB_NAME,
          connectionLimit: 5,
          //debug: true,
        });
      }
    });

    after(async () => {
      if (dbConnection) {
        await dbConnection.end();
      }
    });

    it(`should remove all lines in ${dbPrefix}stock_mvt`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'truncateTable', baseContext);

      await dbConnection.execute(`TRUNCATE TABLE ${dbPrefix}stock_mvt`);

      const [rows] = await dbConnection.query({
        sql: `SELECT * FROM ${dbPrefix}stock_mvt`,
        rowsAsArray: true,
      });
      expect(rows).to.be.length(0);
    });
  });
}

export default cleanTableStockMovements;
