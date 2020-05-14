require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const AddSqlQueryPage = require('@pages/BO/advancedParameters/database/sqlManager/add');
const ViewQueryManagerPage = require('@pages/BO/advancedParameters/database/sqlManager/view');
// Import pages
const SQLQueryFaker = require('@data/faker/sqlQuery');
const {Tables} = require('@data/demo/sqlTables');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_exportSqlQuery';

let browser;
let page;
let numberOfSQLQuery = 0;
const sqlQueryData = new SQLQueryFaker({tableName: 'ps_alias'});
let fileName;
const fileContent = `${Tables.ps_alias.columns[1]};${Tables.ps_alias.columns[2]};${Tables.ps_alias.columns[3]}`;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    sqlManagerPage: new SqlManagerPage(page),
    addSqlQueryPage: new AddSqlQueryPage(page),
    viewQueryManagerPage: new ViewQueryManagerPage(page),
  };
};

describe('Export SQL query', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to database page
  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToCreateNewSQLQuery', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.advancedParametersLink,
      this.pageObjects.boBasePage.databaseLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);
    numberOfSQLQuery = await this.pageObjects.sqlManagerPage.resetAndGetNumberOfLines();
    if (numberOfSQLQuery !== 0) {
      await expect(numberOfSQLQuery).to.be.above(0);
    }
  });

  describe('Create new SQL query', async () => {
    it('should go to \'New SQL query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSQLQueryPage', baseContext);
      await this.pageObjects.sqlManagerPage.goToNewSQLQueryPage();
      const pageTitle = await this.pageObjects.addSqlQueryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSqlQueryPage.pageTitle);
    });

    it('should create new SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewSQLQuery', baseContext);
      const textResult = await this.pageObjects.addSqlQueryPage.createEditSQLQuery(sqlQueryData);
      await expect(textResult).to.equal(this.pageObjects.addSqlQueryPage.successfulCreationMessage);
    });
  });

  describe('Export SQL query', async () => {
    it('should export sql query to a csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'exportSqlQuery', baseContext);
      await this.pageObjects.sqlManagerPage.exportSqlResultDataToCsv();
      const doesFileExist = await files.doesFileExist('request_', 5000, true, 'csv');
      await expect(doesFileExist, 'Export of data has failed').to.be.true;
    });

    it('should check existence of query result data in csv file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSqlQueryInCsvFile', baseContext);
      const numberOfQuery = await this.pageObjects.sqlManagerPage.getNumberOfElementInGrid();
      fileName = await files.getFileNameFromDir(global.BO.DOWNLOAD_PATH, 'request_', '.csv');
      for (let row = 1; row <= numberOfQuery; row++) {
        const textExist = await files.isTextInFile(fileName, fileContent, true, true);
        await expect(textExist, `${fileContent} was not found in the file`).to.be.true;
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should go to \'SQL Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToDeleteSQLQuery', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.advancedParametersLink,
        this.pageObjects.boBasePage.databaseLink,
      );
      const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);
      await this.pageObjects.sqlManagerPage.resetFilter();
      await this.pageObjects.sqlManagerPage.filterSQLQuery('name', sqlQueryData.name);
      const sqlQueryName = await this.pageObjects.sqlManagerPage.getTextColumnFromTable(1, 'name');
      await expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);
      const textResult = await this.pageObjects.sqlManagerPage.deleteSQLQuery(1);
      await expect(textResult).to.equal(this.pageObjects.sqlManagerPage.successfulDeleteMessage);
      const numberOfSQLQueryAfterDelete = await this.pageObjects.sqlManagerPage.resetAndGetNumberOfLines();
      await expect(numberOfSQLQueryAfterDelete).to.be.equal(numberOfSQLQuery);
    });
  });
});
