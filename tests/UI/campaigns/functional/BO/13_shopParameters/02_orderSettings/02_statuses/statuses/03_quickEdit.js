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

// Import data
const {Statuses} = require('@data/demo/orderStatuses');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_QuickEditOrderStatus';

let browserContext;
let page;
let numberOfOrderStatuses = 0;
const tableName = 'order';

/*
Quick edit send email to customer
Quick edit delivery
Quick edit invoice
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Quick edit order status in BO', async () => {
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

  it('should filter by status name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfOrderStatuses).to.be.above(0);

    await statusesPage.filterTable(page, tableName, 'input', 'name', Statuses.shipped.status);

    const numberOfLinesAfterFilter = await statusesPage.getNumberOfElementInGrid(page, tableName);
    await expect(numberOfLinesAfterFilter).to.be.above(0);

    for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
      const textColumn = await statusesPage.getTextColumn(page, tableName, row, 'name');
      await expect(textColumn).to.contains(Statuses.shipped.status);
    }
  });

  const statuses = [
    {
      args: {
        status: 'disable', enable: false, columnName: 'send_email',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'send_email',
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'delivery',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'delivery',
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'invoice',
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'invoice',
      },
    },
  ];

  statuses.forEach((orderStatus, index) => {
    it(`should ${orderStatus.args.status} ${orderStatus.args.columnName} by quick edit`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${orderStatus.args.status}${index}`, baseContext);

      const isActionPerformed = await statusesPage.setStatus(
        page,
        1,
        orderStatus.args.columnName,
        orderStatus.args.enable,
      );

      if (isActionPerformed) {
        const resultMessage = await statusesPage.getGrowlMessageContent(page);
        await expect(resultMessage).to.contains(statusesPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await statusesPage.getStatus(page, 1, orderStatus.args.columnName);
      await expect(currentStatus).to.be.equal(orderStatus.args.enable);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
  });
});
