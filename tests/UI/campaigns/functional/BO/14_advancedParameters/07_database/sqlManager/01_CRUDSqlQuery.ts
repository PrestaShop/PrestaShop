// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSqlManagerPage,
  boSqlManagerCreatePage,
  boSqlManagerViewPage,
  type BrowserContext,
  dataSqlTables,
  FakerSqlQuery,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_CRUDSqlQuery';

describe('BO - Advanced Parameters - Database : Create, View, update and delete SQL query', async () => {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;
  const sqlQueryData: FakerSqlQuery = new FakerSqlQuery({tableName: `${dbPrefix}alias`});
  const editSqlQueryData: FakerSqlQuery = new FakerSqlQuery({name: `edit${sqlQueryData.name}`, tableName: `${dbPrefix}access`});

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSQLQuery: number = 0;

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

    await boSqlManagerPage.closeSfToolBar(page);

    const pageTitle = await boSqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstResetFilter', baseContext);

    numberOfSQLQuery = await boSqlManagerPage.resetAndGetNumberOfLines(page);

    if (numberOfSQLQuery !== 0) {
      expect(numberOfSQLQuery).to.be.above(0);
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

  describe('View SQL query created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedSQLQuery', baseContext);

      await boSqlManagerPage.resetFilter(page);
      await boSqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewNewSQLQueryPage', baseContext);

      await boSqlManagerPage.goToViewSQLQueryPage(page, 1);

      const pageTitle = await boSqlManagerViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerViewPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewSQLQueryResultNumber', baseContext);

      const sqlQueryNumber = await boSqlManagerViewPage.getSQLQueryResultNumber(page);
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForNewSQLQuery', baseContext);

      for (let i = 0; i <= dataSqlTables.ps_alias.columns.length - 1; i++) {
        const columnNameText = await boSqlManagerViewPage.getColumnName(page, i + 1);
        expect(columnNameText).to.be.equal(dataSqlTables.ps_alias.columns[i]);
      }
    });
  });

  describe('Update SQL query created', async () => {
    it('should go to \'Advanced Parameters > Database\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabaseToUpdateSQLQuery', baseContext);

      await boSqlManagerViewPage.goToSubMenu(
        page,
        boSqlManagerViewPage.advancedParametersLink,
        boSqlManagerViewPage.databaseLink,
      );

      const pageTitle = await boSqlManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEditSqlQuery', baseContext);

      await boSqlManagerPage.resetFilter(page);
      await boSqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

      const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(sqlQueryData.name);
    });

    it('should go to edit \'SQL Query\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boSqlManagerPage.goToEditSQLQueryPage(page, 1);

      const pageTitle = await boSqlManagerCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerCreatePage.editPageTitle);
    });

    it('should update SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateSQLQuery', baseContext);

      const textResult = await boSqlManagerCreatePage.createEditSQLQuery(page, editSqlQueryData);
      expect(textResult).to.equal(boSqlManagerCreatePage.successfulUpdateMessage);

      const numberOfSQLQueryAfterUpdate = await boSqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueryAfterUpdate).to.be.equal(numberOfSQLQuery + 1);
    });
  });

  describe('View SQL query updated', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedSQlQuery', baseContext);

      await boSqlManagerPage.resetFilter(page);
      await boSqlManagerPage.filterSQLQuery(page, 'name', editSqlQueryData.name);

      const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(editSqlQueryData.name);
    });

    it('should click on view button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewUpdatedSQLQueryPage', baseContext);

      await boSqlManagerPage.goToViewSQLQueryPage(page, 1);

      const pageTitle = await boSqlManagerViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerViewPage.pageTitle);
    });

    it('should check sql query result number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedSQLQueryResultNumber', baseContext);

      const sqlQueryNumber = await boSqlManagerViewPage.getSQLQueryResultNumber(page);
      expect(sqlQueryNumber).to.be.above(0);
    });

    it('should check columns name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkColumnsNameForUpdatedSQLQuery', baseContext);

      for (let i = 0; i <= dataSqlTables.ps_access.columns.length - 1; i++) {
        const columnNameText = await boSqlManagerViewPage.getColumnName(page, i + 1);
        expect(columnNameText).to.be.equal(dataSqlTables.ps_access.columns[i]);
      }
    });
  });

  describe('Delete SQL query', async () => {
    it('should go to \'Advanced Parameters > Database\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDatabasePageToDeleteSQLQuery', baseContext);

      await boSqlManagerViewPage.goToSubMenu(
        page,
        boSqlManagerViewPage.advancedParametersLink,
        boSqlManagerViewPage.databaseLink,
      );

      const pageTitle = await boSqlManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteSQLQuery', baseContext);

      await boSqlManagerPage.resetFilter(page);
      await boSqlManagerPage.filterSQLQuery(page, 'name', editSqlQueryData.name);

      const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(sqlQueryName).to.contains(editSqlQueryData.name);
    });

    it('should delete SQL query', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

      const textResult = await boSqlManagerPage.deleteSQLQuery(page, 1);
      expect(textResult).to.equal(boSqlManagerPage.successfulDeleteMessage);

      const numberOfSQLQueryAfterDelete = await boSqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfSQLQueryAfterDelete).to.be.equal(numberOfSQLQuery);
    });
  });
});
