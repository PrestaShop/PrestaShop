require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const sqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const addSqlQueryPage = require('@pages/BO/advancedParameters/database/sqlManager/add');

// Import pages
const SQLQueryFaker = require('@data/faker/sqlQuery');
const {Tables} = require('@data/demo/sqlTables');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_sqlManager_exportSqlQuery';

let browserContext;
let page;
let filePath;

let numberOfSQLQueries = 0;

const dbPrefix = global.INSTALL.DB_PREFIX;
const sqlQueryData = new SQLQueryFaker({tableName: `${dbPrefix}alias`});
const fileContent = `${Tables.ps_alias.columns[1]};${Tables.ps_alias.columns[2]};${Tables.ps_alias.columns[3]}`;

describe('Export SQL query', async () => {
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
    await expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);

    numberOfSQLQueries = await sqlManagerPage.resetAndGetNumberOfLines(page);
    if (numberOfSQLQueries !== 0) {
      await expect(numberOfSQLQueries).to.be.above(0);
    }
  });

  describe('Create new SQL query', async () => {
    it('should go to \'New SQL query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSQLQueryPage', baseContext);

      await sqlManagerPage.goToNewSQLQueryPage(page);
      const pageTitle = await addSqlQueryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addSqlQueryPage.pageTitle);
    });

    it('should create new SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewSQLQuery', baseContext);

      const textResult = await addSqlQueryPage.createEditSQLQuery(page, sqlQueryData);
      await expect(textResult).to.equal(addSqlQueryPage.successfulCreationMessage);
    });
  });

  describe('Export SQL query', async () => {
    it('should export sql query to a csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'exportSqlQuery', baseContext);

      filePath = await sqlManagerPage.exportSqlResultDataToCsv(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'Export of data has failed').to.be.true;
    });

    it('should check existence of query result data in csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSqlQueryInCsvFile', baseContext);

      const numberOfQuery = await sqlManagerPage.getNumberOfElementInGrid(page);

      for (let row = 1; row <= numberOfQuery; row++) {
        const textExist = await files.isTextInFile(filePath, fileContent, true, true);
        await expect(textExist, `${fileContent} was not found in the file`).to.be.true;
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);

      await sqlManagerPage.resetFilter(page);

      await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      await expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

      const textResult = await sqlManagerPage.deleteSQLQuery(page, 1);
      await expect(textResult).to.equal(sqlManagerPage.successfulDeleteMessage);

      const numberOfSQLQueriesAfterDelete = await sqlManagerPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSQLQueriesAfterDelete).to.be.equal(numberOfSQLQueries);
    });
  });
});
