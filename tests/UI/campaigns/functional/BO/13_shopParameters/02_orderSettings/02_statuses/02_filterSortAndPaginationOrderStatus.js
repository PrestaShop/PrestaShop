require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

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

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_filterSortAndPaginationOrderStatus';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfOrderStatuses = 0;

/*
Filter order status
 */
describe('Filter, sort and pagination order status', async () => {
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

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page);
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
            idColumn: 1,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Statuses.shipped.status,
            idColumn: 2,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterBySendEmail',
            filterType: 'select',
            filterBy: 'send_email',
            filterValue: true,
            idColumn: 4,
          },
        expected: 'Enabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByDelivery',
            filterType: 'select',
            filterBy: 'delivery',
            filterValue: true,
            idColumn: 5,
          },
        expected: 'Enabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByInvoice',
            filterType: 'select',
            filterBy: 'invoice',
            filterValue: false,
            idColumn: 6,
          },
        expected: 'Disabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByEmailTemplate',
            filterType: 'input',
            filterBy: 'template',
            filterValue: Statuses.canceled.emailTemplate,
            idColumn: 7,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await statusesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page);
        await expect(numberOfLinesAfterFilter).to.be.at.most(numberOfOrderStatuses);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await statusesPage.getTextColumn(page, row, test.args.filterBy, test.args.idColumn);
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
      });
    });
  });

  // 1 - Create 2 order statuses
  const creationTests = new Array(2).fill(0, 0, 2);

  creationTests.forEach((test, index) => {
    describe(`Create order status n°${index + 1} in BO`, async () => {
      const orderStatusData = new OrderStatusFaker({name: `todelete${index}`});

      it('should go to add new tax rule group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddOrderStatusPage${index}`, baseContext);

        await statusesPage.goToNewOrderStatusPage(page);

        const pageTitle = await addOrderStatusPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
      });

      it('should create order status and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addOrderStatusPage.setOrderStatus(page, orderStatusData);
        await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page);
        await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + index + 1);
      });
    });
  });
});
