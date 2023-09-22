// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import addSqlQueryPage from '@pages/BO/advancedParameters/database/sqlManager/add';

// Import data
import SqlQueryData from '@data/faker/sqlQuery';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_filterSortAndPagination';

/*
Create 11 SQL queries
Pagination next and previous
Filter SQL queries by : Id, Name, sql query
Sort SQL queries by : Id, Name, sql query
Delete by bulk actions
 */
describe('BO - Advanced Parameters - Database : Filter, sort and pagination SQL manager table', async () => {
  const dbPrefix: string = global.INSTALL.DB_PREFIX;

  let browserContext: BrowserContext;
  let page: Page;
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

  // 1 - Create 11 SQL queries
  describe('Create 11 SQL queries in BO', async () => {
    const creationTests: number[] = new Array(11).fill(0, 0, 11);
    creationTests.forEach((test: number, index: number) => {
      const sqlQueryData = new SqlQueryData({name: `todelete${index}`, tableName: `${dbPrefix}alias`});

      it('should go to add new SQL query page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddSqlQueryPage${index}`, baseContext);

        await sqlManagerPage.goToNewSQLQueryPage(page);

        const pageTitle = await addSqlQueryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addSqlQueryPage.pageTitle);
      });

      it(`should create SQL query n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addSqlQueryPage.createEditSQLQuery(page, sqlQueryData);
        expect(textResult).to.contains(sqlManagerPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await sqlManagerPage.getNumberOfElementInGrid(page);
        expect(numberOfLinesAfterCreation).to.be.equal(numberOfSQLQueries + index + 1);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await sqlManagerPage.selectPaginationLimit(page, 10);
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

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await sqlManagerPage.selectPaginationLimit(page, 50);
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
          filterValue: '5',
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

    tests.forEach((test, index: number) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await sqlManagerPage.filterSQLQuery(page, test.args.filterBy, test.args.filterValue);

        const numberOfLinesAfterFilter = await sqlManagerPage.getNumberOfElementInGrid(page);
        expect(numberOfLinesAfterFilter).to.be.at.most(numberOfSQLQueries + 11);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await sqlManagerPage.getTextColumnFromTable(page, row, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter${index}`, baseContext);

        numberOfSQLQueries = await sqlManagerPage.resetAndGetNumberOfLines(page);
        expect(numberOfSQLQueries).to.be.above(0);
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

        const nonSortedTable = await sqlManagerPage.getAllRowsColumnContent(page, test.args.sortBy);
        await sqlManagerPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await sqlManagerPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 5 - Delete created SQL queries by bulk actions
  describe('Delete sql queries with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await sqlManagerPage.filterSQLQuery(page, 'name', 'todelete');

      const textResult = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
      expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await sqlManagerPage.deleteWithBulkActions(page);
      expect(deleteTextResult).to.be.equal(sqlManagerPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLinesAfterReset = await sqlManagerPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.equal(numberOfSQLQueries - 11);
    });
  });
});
