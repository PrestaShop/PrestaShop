require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const statusesPage = require('@pages/BO/shopParameters/orderSettings/statuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_QuickEditOrderStatus';

// Import expect from chai
const {expect} = require('chai');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');

let browserContext;
let page;
let numberOfOrderStatuses = 0;
const tableName = 'order';

/*
Quick edit send email to customer
Quick edit delivery
Quick edit invoice
 */
describe('Quick edit order status in BO', async () => {
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
      const textColumn = await statusesPage.getTextColumn(page, tableName, row, 'name', 2);
      await expect(textColumn).to.contains(Statuses.shipped.status);
    }
  });

  const statuses = [
    {
      args: {
        status: 'disable', enable: false, columnName: 'Send email to customer', columnID: 4,
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'Send email to customer', columnID: 4,
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'Delivery', columnID: 5,
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'Delivery', columnID: 5,
      },
    },
    {
      args: {
        status: 'disable', enable: false, columnName: 'Invoice', columnID: 6,
      },
    },
    {
      args: {
        status: 'enable', enable: true, columnName: 'Invoice', columnID: 6,
      },
    },
  ];

  statuses.forEach((orderStatus, index) => {
    it(`should ${orderStatus.args.status} ${orderStatus.args.columnName} by quick edit`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${orderStatus.args.status}${index}`, baseContext);

      const isActionPerformed = await statusesPage.setStatus(
        page,
        1,
        orderStatus.args.columnID,
        orderStatus.args.enable,
      );

      if (isActionPerformed) {
        const resultMessage = await statusesPage.getGrowlMessageContent(page);
        await expect(resultMessage).to.contains(statusesPage.successfulUpdateStatusMessage);
      }

      const currentStatus = await statusesPage.getStatus(page, 1, orderStatus.args.columnID);
      await expect(currentStatus).to.be.equal(orderStatus.args.enable);
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfLinesAfterReset).to.equal(numberOfOrderStatuses);
  });
});
