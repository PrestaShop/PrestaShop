require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');
const addOrderReturnStatusPage = require('@pages/BO/shopParameters/orderSettings/statuses/returnStatus/add');

// Import data
const {ReturnStatuses} = require('@data/demo/orderReturnStatuses');
const OrderReturnStatusFaker = require('@data/faker/orderReturnStatus');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_returnStatuses_filterSortAndPagination';

let browserContext;
let page;
let numberOfOrderReturnStatuses = 0;
const tableName = 'order_return';

/*
Filter order return status by : Id, Name
Sort order return status by : Id, Name
Create 16 order return statuses
Pagination next and previous
Delete by bulk actions
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Filter, sort and '
  + 'pagination order return status', async () => {
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
  describe('Sort order return statuses table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc',
          sortBy: 'id_order_return_state',
          columnID: 1,
          sortDirection: 'down',
          isFloat: true,
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
          testIdentifier: 'sortByIdAsc',
          sortBy: 'id_order_return_state',
          columnID: 1,
          sortDirection: 'up',
          isFloat: true,
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

  // 3 - Create 16 order return statuses
  const creationTests = new Array(16).fill(0, 0, 16);

  creationTests.forEach((test, index) => {
    describe(`Create order return status n°${index + 1} in BO`, async () => {
      const orderReturnStatusData = new OrderReturnStatusFaker({name: `todelete${index}`});

      it('should go to add new order status group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddOrderReturnStatusPage${index}`, baseContext);

        await statusesPage.goToNewOrderReturnStatusPage(page);

        const pageTitle = await addOrderReturnStatusPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderReturnStatusPage.pageTitleCreate);
      });

      it('should create order status and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderReturnStatus${index}`, baseContext);

        await addOrderReturnStatusPage.setOrderReturnStatus(page, orderReturnStatusData);
        /* Successful message is not visible, skipping it */
        /* https://github.com/PrestaShop/PrestaShop/issues/21270 */
        // await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
        await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderReturnStatuses + index + 1);
      });
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await statusesPage.selectPaginationLimit(page, tableName, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await statusesPage.paginationNext(page, tableName);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await statusesPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await statusesPage.selectPaginationLimit(page, tableName, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 : Delete order return statuses created with bulk actions
  describe('Delete order return statuses with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await statusesPage.filterTable(page, tableName, 'input', 'name', 'todelete');
      const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await statusesPage.getTextColumn(page, tableName, i, 'name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete order return statuses with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStatus', baseContext);

      const deleteTextResult = await statusesPage.bulkDeleteOrderStatuses(page, tableName);
      await expect(deleteTextResult).to.be.contains(statusesPage.successfulMultiDeleteMessage);
    });
    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfLinesAfterReset).to.be.equal(numberOfOrderReturnStatuses);
    });
  });
});
