// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import addSqlQueryPage from '@pages/BO/advancedParameters/database/sqlManager/add';
import viewQueryManagerPage from '@pages/BO/advancedParameters/database/sqlManager/view';

// Import data
import Tables from '@data/demo/sqlTables';
import SqlQueryData from '@data/faker/sqlQuery';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_CRUDSqlQuery';

describe('BO - Advanced Parameters - Database : Create, View, update and delete SQL query', async () => {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;
  const sqlQueryData: SqlQueryData = new SqlQueryData({tableName: `${dbPrefix}alias`});
  const editSqlQueryData: SqlQueryData = new SqlQueryData({name: `edit${sqlQueryData.name}`, tableName: `${dbPrefix}access`});

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSQLQuery: number = 0;

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

    await sqlManagerPage.closeSfToolBar(page);

    const pageTitle = await sqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);

    numberOfSQLQuery = await sqlManagerPage.resetAndGetNumberOfLines(page);

    if (numberOfSQLQuery !== 0) {
      expect(numberOfSQLQuery).to.be.above(0);
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

  describe('View SQL query created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedSQLQuery', baseContext);

      await sqlManagerPage.resetFilter(page);
      await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewNewSQLQueryPage', baseContext);

      await sqlManagerPage.goToViewSQLQueryPage(page, 1);

      const pageTitle = await viewQueryManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewQueryManagerPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewSQLQueryResultNumber', baseContext);

      const sqlQueryNumber = await viewQueryManagerPage.getSQLQueryResultNumber(page);
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForNewSQLQuery', baseContext);

      for (let i = 0; i <= Tables.ps_alias.columns.length - 1; i++) {
        const columnNameText = await viewQueryManagerPage.getColumnName(page, i + 1);
        expect(columnNameText).to.be.equal(Tables.ps_alias.columns[i]);
      }
    });
  });

  describe('Update SQL query created', async () => {
    it('should go to \'Advanced Parameters > Database\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseToUpdateSQLQuery', baseContext);

      await viewQueryManagerPage.goToSubMenu(
        page,
        viewQueryManagerPage.advancedParametersLink,
        viewQueryManagerPage.databaseLink,
      );

      const pageTitle = await sqlManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEditSqlQuery', baseContext);

      await sqlManagerPage.resetFilter(page);
      await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should go to edit \'SQL Query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await sqlManagerPage.goToEditSQLQueryPage(page, 1);

      const pageTitle = await addSqlQueryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addSqlQueryPage.editPageTitle);
    });

    it('should update SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSQLQuery', baseContext);

      const textResult = await addSqlQueryPage.createEditSQLQuery(page, editSqlQueryData);
      expect(textResult).to.equal(addSqlQueryPage.successfulUpdateMessage);

      const numberOfSQLQueryAfterUpdate = await sqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueryAfterUpdate).to.be.equal(numberOfSQLQuery + 1);
    });
  });

  describe('View SQL query updated', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedSQlQuery', baseContext);

      await sqlManagerPage.resetFilter(page);
      await sqlManagerPage.filterSQLQuery(page, 'name', editSqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(editSqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedSQLQueryPage', baseContext);

      await sqlManagerPage.goToViewSQLQueryPage(page, 1);

      const pageTitle = await viewQueryManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewQueryManagerPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedSQLQueryResultNumber', baseContext);

      const sqlQueryNumber = await viewQueryManagerPage.getSQLQueryResultNumber(page);
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForUpdatedSQLQuery', baseContext);

      for (let i = 0; i <= Tables.ps_access.columns.length - 1; i++) {
        const columnNameText = await viewQueryManagerPage.getColumnName(page, i + 1);
        expect(columnNameText).to.be.equal(Tables.ps_access.columns[i]);
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should go to \'Advanced Parameters > Database\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToDeleteSQLQuery', baseContext);

      await viewQueryManagerPage.goToSubMenu(
        page,
        viewQueryManagerPage.advancedParametersLink,
        viewQueryManagerPage.databaseLink,
      );

      const pageTitle = await sqlManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);

      await sqlManagerPage.resetFilter(page);
      await sqlManagerPage.filterSQLQuery(page, 'name', editSqlQueryData.name);

      const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(editSqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

      const textResult = await sqlManagerPage.deleteSQLQuery(page, 1);
      expect(textResult).to.equal(sqlManagerPage.successfulDeleteMessage);

      const numberOfSQLQueryAfterDelete = await sqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueryAfterDelete).to.be.equal(numberOfSQLQuery);
    });
  });
});
