require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');
const addOrderStatusPage = require('@pages/BO/shopParameters/orderSettings/statuses/add');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const OrderStatusFaker = require('@data/faker/orderStatus');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_filterSortAndPagination';

let browserContext;
let page;
let numberOfOrderStatuses = 0;
const tableName = 'order';

/*
Filter order status by : Id, Name, Send email to customer, Delivery, Invoice, email template
Sort order status by : Id, Name, Email template
Create 8 order statuses
Pagination next and previous
Delete by bulk actions
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Filter, sort and pagination order status', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    for (let i = 0; i <= 2; i++) {
      await files.deleteFile(`todelete${i}.jpg`);
    }
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

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfOrderStatuses).to.be.above(0);
  });

  // 1 - Filter order statuses
  describe('Filter order statuses table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_order_state',
            filterValue: Statuses.paymentAccepted.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Statuses.shipped.status,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySendEmail',
            filterType: 'select',
            filterBy: 'send_email',
            filterValue: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByDelivery',
            filterType: 'select',
            filterBy: 'delivery',
            filterValue: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByInvoice',
            filterType: 'select',
            filterBy: 'invoice',
            filterValue: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByEmailTemplate',
            filterType: 'input',
            filterBy: 'template',
            filterValue: Statuses.canceled.emailTemplate,
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
        await expect(numberOfLinesAfterFilter).to.be.at.most(numberOfOrderStatuses);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          if (typeof test.args.filterValue === 'boolean') {
            const columnStatus = await statusesPage.getStatus(page, row, test.args.filterBy);
            await expect(columnStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await statusesPage.getTextColumn(
              page,
              tableName,
              row,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
        await expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
      });
    });
  });

  // 2 - Sort order statuses table
  describe('Sort order statuses table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_order_state', columnID: 1, sortDirection: 'down', isFloat: true,
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
          testIdentifier: 'sortByTemplateAsc', sortBy: 'template', columnID: 7, sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByTemplateDesc', sortBy: 'template', columnID: 7, sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_order_state', columnID: 1, sortDirection: 'up', isFloat: true,
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

  // 3 - Create 8 order statuses
  const creationTests = new Array(8).fill(0, 0, 8);

  creationTests.forEach((test, index) => {
    describe(`Create order status n°${index + 1} in BO`, async () => {
      before(() => files.generateImage(`todelete${index}.jpg`));

      const orderStatusData = new OrderStatusFaker({name: `todelete${index}`});

      it('should go to add new order status group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddOrderStatusPage${index}`, baseContext);

        await statusesPage.goToNewOrderStatusPage(page);

        const pageTitle = await addOrderStatusPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
      });

      it('should create order status and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addOrderStatusPage.setOrderStatus(page, orderStatusData);
        await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
        await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + index + 1);
      });

      after(() => files.deleteFile(`todelete${index}.jpg`));
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

  // 5 : Delete order statuses created with bulk actions
  describe('Delete order statuses with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        'todelete',
      );

      const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await statusesPage.getTextColumn(page, tableName, i, 'name', 3);
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete order statuses with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStatus', baseContext);

      const deleteTextResult = await statusesPage.bulkDeleteOrderStatuses(page, tableName);
      await expect(deleteTextResult).to.be.contains(statusesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfLinesAfterReset).to.be.equal(numberOfOrderStatuses);
    });
  });
});
