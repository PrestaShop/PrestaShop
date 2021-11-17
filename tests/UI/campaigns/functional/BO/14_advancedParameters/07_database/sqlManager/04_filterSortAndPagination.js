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

const dbPrefix = global.INSTALL.DB_PREFIX;

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_database_sqlManager_filterSortAndPagination';

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
      const sqlQueryData = new SQLQueryFaker({name: `todelete${index}`, tableName: `${dbPrefix}alias`});

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

  // 3 - Filter SQL manager table
  describe('Filter SQL manager table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterById',
          filterType: 'input',
          filterBy: 'id_request_sql',
          filterValue: 5,
        },
      },
      {
        args: {
          testIdentifier: 'filterByName',
          filterType: 'input',
          filterBy: 'name',
          filterValue: 'todelete5',
        },
      },
      {
        args: {
          testIdentifier: 'filterBySqlQuery',
          filterType: 'input',
          filterBy: 'sql',
          filterValue: `select * from ${dbPrefix}alias`,
        },
      },
    ];

    tests.forEach((test, index) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await sqlManagerPage.filterSQLQuery(page, test.args.filterBy, test.args.filterValue);

        const numberOfLinesAfterFilter = await sqlManagerPage.getNumberOfElementInGrid(page);
        await expect(numberOfLinesAfterFilter).to.be.at.most(numberOfSQLQueries + 11);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await sqlManagerPage.getTextColumnFromTable(page, row, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${index}`, baseContext);

        numberOfSQLQueries = await sqlManagerPage.resetAndGetNumberOfLines(page);
        await expect(numberOfSQLQueries).to.be.above(0);
      });
    });
  });

  // 4 - Sort SQL manager table
  describe('Sort SQL manager table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_request_sql', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortBySQLAsc', sortBy: 'sql', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortBySQLDesc', sortBy: 'sql', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_request_sql', sortDirection: 'asc', isFloat: true,
        },
      },
    ];
    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await sqlManagerPage.getAllRowsColumnContent(page, test.args.sortBy);
        await sqlManagerPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await sqlManagerPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await sqlManagerPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 5 - Delete created SQL queries by bulk actions
  describe('Delete sql queries with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await sqlManagerPage.filterSQLQuery(
        page,
        'name',
        'todelete',
      );

      const textResult = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await sqlManagerPage.deleteWithBulkActions(page);
      await expect(deleteTextResult).to.be.equal(sqlManagerPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLinesAfterReset = await sqlManagerPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLinesAfterReset).to.equal(numberOfSQLQueries - 11);
    });
  });
});
