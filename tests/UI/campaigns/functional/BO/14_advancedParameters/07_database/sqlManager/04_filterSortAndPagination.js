require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const sqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');
const addSqlQueryPage = require('@pages/BO/advancedParameters/database/sqlManager/add');

// Import data
const SQLQueryFaker = require('@data/faker/sqlQuery');
const {Tables} = require('@data/demo/sqlTables');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParams_database_sqlManager_filterSortAndPagination';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfSQLQueries = 0;

/*
Create 11 SQL queries
Pagination next and previous
Filter SQL queries by : Id, Name, sql query
Sort SQL queries by : Id, Name, sql query
Delete by bulk actions
 */
describe('Filter, sort and pagination SQL manager', async () => {
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

  // 1 - Create 11 SQL queries
  const creationTests = new Array(11).fill(0, 0, 11);

  creationTests.forEach((test, index) => {
    describe(`Create SQL query n°${index + 1} in BO`, async () => {
      const sqlQueryData = new SQLQueryFaker({name: `todelete${index}`, tableName: 'ps_alias'});

      it('should go to add new SQL manager group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddSqlQueryPage${index}`, baseContext);

        await sqlManagerPage.goToNewSQLQueryPage(page);

        const pageTitle = await addSqlQueryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addSqlQueryPage.pageTitle);
      });

      it('should create SQL manager and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addSqlQueryPage.createEditSQLQuery(page, sqlQueryData);
        await expect(textResult).to.contains(sqlManagerPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await sqlManagerPage.getNumberOfElementInGrid(page);
        await expect(numberOfLinesAfterCreation).to.be.equal(numberOfSQLQueries + index + 1);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await sqlManagerPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await sqlManagerPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await sqlManagerPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await sqlManagerPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });
});
