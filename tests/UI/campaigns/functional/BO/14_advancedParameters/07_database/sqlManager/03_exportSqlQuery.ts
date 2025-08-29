// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSqlManagerPage,
  boSqlManagerCreatePage,
  type BrowserContext,
  dataSqlTables,
  FakerSqlQuery,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_exportSqlQuery';

describe('BO - Advanced Parameters - Database : Export SQL query', async () => {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;
  const sqlQueryData: FakerSqlQuery = new FakerSqlQuery({tableName: `${dbPrefix}alias`});
  const fileContent: string = `${dataSqlTables.ps_alias.columns[1]};`
    + `${dataSqlTables.ps_alias.columns[2]};`
    + `${dataSqlTables.ps_alias.columns[3]}`;

  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  let numberOfSQLQueries: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Go to database page
  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToCreateNewSQLQuery', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.databaseLink,
    );
    await boDashboardPage.closeSfToolBar(page);

    const pageTitle = await boSqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);

    numberOfSQLQueries = await boSqlManagerPage.resetAndGetNumberOfLines(page);
    if (numberOfSQLQueries !== 0) {
      expect(numberOfSQLQueries).to.be.above(0);
    }
  });

  describe('Create new SQL query', async () => {
    it('should go to \'New SQL query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSQLQueryPage', baseContext);

      await boSqlManagerPage.goToNewSQLQueryPage(page);

      const pageTitle = await boSqlManagerCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerCreatePage.pageTitle);
    });

    it('should create new SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewSQLQuery', baseContext);

      const textResult = await boSqlManagerCreatePage.createEditSQLQuery(page, sqlQueryData);
      expect(textResult).to.equal(boSqlManagerCreatePage.successfulCreationMessage);
    });
  });

  describe('Export SQL query', async () => {
    it('should export sql query to a csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'exportSqlQuery', baseContext);

      filePath = await boSqlManagerPage.exportSqlResultDataToCsv(page);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'Export of data has failed').to.eq(true);
    });

    it('should check existence of query result data in csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSqlQueryInCsvFile', baseContext);

      const numberOfQuery = await boSqlManagerPage.getNumberOfElementInGrid(page);

      for (let row = 1; row <= numberOfQuery; row++) {
        const textExist = await utilsFile.isTextInFile(filePath, fileContent, true, true);
        expect(textExist, `${fileContent} was not found in the file`).to.eq(true);
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);

      await boSqlManagerPage.resetFilter(page);
      await boSqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

      const textResult = await boSqlManagerPage.deleteSQLQuery(page, 1);
      expect(textResult).to.equal(boSqlManagerPage.successfulDeleteMessage);

      const numberOfSQLQueriesAfterDelete = await boSqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueriesAfterDelete).to.be.equal(numberOfSQLQueries);
    });
  });
});
