// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import addSqlQueryPage from '@pages/BO/advancedParameters/database/sqlManager/add';

// Import data
import Tables from '@data/demo/sqlTables';
import SQLQueryFaker from '@data/faker/sqlQuery';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_exportSqlQuery';

describe('BO - Advanced Parameters - Database : Export SQL query', async () => {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;
  const sqlQueryData: SQLQueryFaker = new SQLQueryFaker({tableName: `${dbPrefix}alias`});
  const fileContent: string = `${Tables.ps_alias.columns[1]};${Tables.ps_alias.columns[2]};${Tables.ps_alias.columns[3]}`;

  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  let numberOfSQLQueries: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Go to database page
  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToCreateNewSQLQuery', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.databaseLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await sqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);

    numberOfSQLQueries = await sqlManagerPage.resetAndGetNumberOfLines(page);
    if (numberOfSQLQueries !== 0) {
      expect(numberOfSQLQueries).to.be.above(0);
    }
  });

  describe('Create new SQL query', async () => {
    it('should go to \'New SQL query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSQLQueryPage', baseContext);

      await sqlManagerPage.goToNewSQLQueryPage(page);

      const pageTitle = await addSqlQueryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addSqlQueryPage.pageTitle);
    });

    it('should create new SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewSQLQuery', baseContext);

      const textResult = await addSqlQueryPage.createEditSQLQuery(page, sqlQueryData);
      expect(textResult).to.equal(addSqlQueryPage.successfulCreationMessage);
    });
  });

  describe('Export SQL query', async () => {
    it('should export sql query to a csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'exportSqlQuery', baseContext);

      filePath = await sqlManagerPage.exportSqlResultDataToCsv(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'Export of data has failed').to.eq(true);
    });

    it('should check existence of query result data in csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSqlQueryInCsvFile', baseContext);

      const numberOfQuery = await sqlManagerPage.getNumberOfElementInGrid(page);

      for (let row = 1; row <= numberOfQuery; row++) {
        const textExist = await files.isTextInFile(filePath, fileContent, true, true);
        expect(textExist, `${fileContent} was not found in the file`).to.eq(true);
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);

      await sqlManagerPage.resetFilter(page);
      await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

      const textResult = await sqlManagerPage.deleteSQLQuery(page, 1);
      expect(textResult).to.equal(sqlManagerPage.successfulDeleteMessage);

      const numberOfSQLQueriesAfterDelete = await sqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueriesAfterDelete).to.be.equal(numberOfSQLQueries);
    });
  });
});
