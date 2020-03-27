require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
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

const baseContext = 'functional_BO_advancedParams_database_CRUDSqlQuery';

let browser;
let page;
let numberOfSQLQuery = 0;
const sqlQueryData = new SQLQueryFaker({tableName: 'ps_alias'});
const editSqlQueryData = new SQLQueryFaker({name: `edit${sqlQueryData.name}`, tableName: 'ps_access'});

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

describe('CRUD SQL query', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
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

  describe('View SQL query created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedSQLQuery', baseContext);
      await this.pageObjects.sqlManagerPage.resetFilter();
      await this.pageObjects.sqlManagerPage.filterSQLQuery('name', sqlQueryData.name);
      const sqlQueryName = await this.pageObjects.sqlManagerPage.getTextColumnFromTable(1, 'name');
      await expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewNewSQLQueryPage', baseContext);
      await this.pageObjects.sqlManagerPage.goToViewSQLQueryPage(1);
      const pageTitle = await this.pageObjects.viewQueryManagerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewQueryManagerPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewSQLQueryResultNumber', baseContext);
      const sqlQueryNumber = await this.pageObjects.viewQueryManagerPage.getSQLQueryResultNumber();
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForNewSQLQuery', baseContext);
      for (let i = 0; i <= Tables.ps_alias.columns.length - 1; i++) {
        const columnNameText = await this.pageObjects.viewQueryManagerPage.getColumnName(i + 1);
        expect(columnNameText).to.be.equal(Tables.ps_alias.columns[i]);
      }
    });
  });

  describe('Update SQL query created', async () => {
    it('should go to \'SQL Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseToUpdateSQLQuery', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.advancedParametersLink,
        this.pageObjects.boBasePage.databaseLink,
      );
      const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEditSqlQuery', baseContext);
      await this.pageObjects.sqlManagerPage.resetFilter();
      await this.pageObjects.sqlManagerPage.filterSQLQuery('name', sqlQueryData.name);
      const sqlQueryName = await this.pageObjects.sqlManagerPage.getTextColumnFromTable(1, 'name');
      await expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should go to edit SQL query page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);
      await this.pageObjects.sqlManagerPage.goToEditSQLQueryPage(1);
      const pageTitle = await this.pageObjects.addSqlQueryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSqlQueryPage.pageTitle);
    });

    it('should update SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSQLQuery', baseContext);
      const textResult = await this.pageObjects.addSqlQueryPage.createEditSQLQuery(editSqlQueryData);
      await expect(textResult).to.equal(this.pageObjects.addSqlQueryPage.successfulUpdateMessage);
      const numberOfSQLQueryAfterUpdate = await this.pageObjects.sqlManagerPage.resetAndGetNumberOfLines();
      await expect(numberOfSQLQueryAfterUpdate).to.be.equal(numberOfSQLQuery + 1);
    });
  });

  describe('View SQL query updated', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedSQlQuery', baseContext);
      await this.pageObjects.sqlManagerPage.resetFilter();
      await this.pageObjects.sqlManagerPage.filterSQLQuery('name', editSqlQueryData.name);
      const sqlQueryName = await this.pageObjects.sqlManagerPage.getTextColumnFromTable(1, 'name');
      await expect(sqlQueryName).to.contains(editSqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedSQLQueryPage', baseContext);
      await this.pageObjects.sqlManagerPage.goToViewSQLQueryPage(1);
      const pageTitle = await this.pageObjects.viewQueryManagerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewQueryManagerPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedSQLQueryResultNumber', baseContext);
      const sqlQueryNumber = await this.pageObjects.viewQueryManagerPage.getSQLQueryResultNumber();
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForUpdatedSQLQuery', baseContext);
      for (let i = 0; i <= Tables.ps_access.columns.length - 1; i++) {
        const columnNameText = await this.pageObjects.viewQueryManagerPage.getColumnName(i + 1);
        expect(columnNameText).to.be.equal(Tables.ps_access.columns[i]);
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
      await this.pageObjects.sqlManagerPage.filterSQLQuery('name', editSqlQueryData.name);
      const sqlQueryName = await this.pageObjects.sqlManagerPage.getTextColumnFromTable(1, 'name');
      await expect(sqlQueryName).to.contains(editSqlQueryData.name);
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
