// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Common commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';
import statusesPage from '@pages/BO/shopParameters/orderSettings/statuses';
import addOrderStatusPage from '@pages/BO/shopParameters/orderSettings/statuses/add';

// Import data
import OrderStatusData from '@data/faker/orderStatus';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_statuses_statuses_bulkActions';

/*
Create 2 order statuses
Delete by bulk actions
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Bulk actions in order statuses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderStatuses: number = 0;

  const tableName: string = 'order';

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
    expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  it('should go to \'Statuses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPage', baseContext);

    await orderSettingsPage.goToStatusesPage(page);

    const pageTitle = await statusesPage.getPageTitle(page);
    expect(pageTitle).to.contains(statusesPage.pageTitle);
  });

  it('should reset all filters and get number of order statuses', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfOrderStatuses = await statusesPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfOrderStatuses).to.be.above(0);
  });

  it('should change the items number to 20 per page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

    const paginationNumber = await statusesPage.selectPaginationLimit(page, tableName, 20);
    expect(paginationNumber).to.contains('(page 1 / 1)');
  });

  // 1 - Create 2 order statuses
  [1, 2].forEach((test: number, index: number) => {
    describe(`Create order status n°${index + 1} in BO`, async () => {
      before(() => files.generateImage(`todelete${index}.jpg`));

      const orderStatusData: OrderStatusData = new OrderStatusData({name: `todelete${index}`});

      it('should go to add new order status group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddOrderStatusPage${index}`, baseContext);

        await statusesPage.goToNewOrderStatusPage(page);

        const pageTitle = await addOrderStatusPage.getPageTitle(page);
        expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
      });

      it('should create order status and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createOrderStatus${index}`, baseContext);

        const textResult = await addOrderStatusPage.setOrderStatus(page, orderStatusData);
        expect(textResult).to.contains(statusesPage.successfulCreationMessage);

        const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + index + 1);
      });

      after(() => files.deleteFile(`todelete${index}.jpg`));
    });
  });

  // 2 : Delete order statuses created with bulk actions
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
        const textColumn = await statusesPage.getTextColumn(page, tableName, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete order statuses with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteStatus', baseContext);

      const deleteTextResult = await statusesPage.bulkDeleteOrderStatuses(page, tableName);
      expect(deleteTextResult).to.be.contains(statusesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfOrderStatuses);
    });
  });
});
