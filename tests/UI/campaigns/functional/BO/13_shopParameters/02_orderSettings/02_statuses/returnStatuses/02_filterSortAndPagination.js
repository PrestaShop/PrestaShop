require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');
const addOrderReturnStatusPage = require('@pages/BO/shopParameters/orderSettings/statuses/returnStatus/add');

// Import data
const {ReturnStatuses} = require('@data/demo/orderReturnStatuses');
const OrderReturnStatusFaker = require('@data/faker/orderStatus');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_'
  + 'returnStatuses_filterSortAndPaginationOrderStatus';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfOrderReturnStatuses = 0;
const tableName = 'order_return';

/*
Filter order return status by : Id, Name
Sort order return status by : Id, Name
Create 2 order return statuses
Pagination next and previous
Delete by bulk actions
 */
describe('Filter, sort and pagination order return status', async () => {
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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.orderSettingsLink,
    );

    const pageTitle = await orderSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  it('should go to \'Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await orderSettingsPage.goToStatusesPage(page);

    const pageTitle = await statusesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(statusesPage.pageTitle);
  });

  it('should reset all filters and get number of order return statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderReturnStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfOrderReturnStatuses).to.be.above(0);
  });

  // 1 - Filter order return statuses
  describe('Filter order return statuses table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_order_return_state',
            filterValue: ReturnStatuses.packageReceived.id,
            idColumn: 1,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: ReturnStatuses.returnCompleted.name,
            idColumn: 2,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await statusesPage.filterTable(
          page,
          tableName,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);
        await expect(numberOfLinesAfterFilter).to.be.at.most(numberOfOrderReturnStatuses);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await statusesPage.getTextColumn(
            page,
            tableName,
            row,
            test.args.filterBy,
            test.args.idColumn,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
        await expect(numberOfLinesAfterReset).to.equal(numberOfOrderReturnStatuses);
      });
    });
  });

  // 2 - Sort order return statuses table
  describe('Sort order retuen statuses table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_order_return_state', columnID: 1, sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', columnID: 2, sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', columnID: 2, sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_order_return_state', columnID: 1, sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await statusesPage.getAllRowsColumnContent(
          page,
          tableName,
          test.args.sortBy,
          test.args.columnID,
        );

        await statusesPage.sortTable(page, tableName, test.args.sortBy, test.args.columnID, test.args.sortDirection);

        let sortedTable = await statusesPage.getAllRowsColumnContent(
          page,
          tableName,
          test.args.sortBy,
          test.args.columnID,
        );

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await statusesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });
});
