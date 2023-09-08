// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';
import statusesPage from '@pages/BO/shopParameters/orderSettings/statuses';
import addOrderStatusPage from '@pages/BO/shopParameters/orderSettings/statuses/add';
import ordersPage from '@pages/BO/orders';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

// Import data
import OrderStatusData from '@data/faker/orderStatus';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_statuses_statuses_CRUDOrderStatus';

/*
Create new order status
View new status in order page
Update order status
Delete order status
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Create, read, update and '
  + 'delete order status in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfOrderStatuses: number = 0;

  const tableName: string = 'order';
  const createOrderStatusData: OrderStatusData = new OrderStatusData();
  const editOrderStatusData: OrderStatusData = new OrderStatusData({name: `edit_${createOrderStatusData.name}`});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(`${createOrderStatusData.name}.jpg`),
      files.generateImage(`${editOrderStatusData.name}.jpg`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      files.deleteFile(`${createOrderStatusData.name}.jpg`),
      files.deleteFile(`${editOrderStatusData.name}.jpg`),
    ]);
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

  // 1 - Create order status
  describe('Create order status', async () => {
    it('should go to add new order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToaddOrderStatusPage', baseContext);

      await statusesPage.goToNewOrderStatusPage(page);

      const pageTitle = await addOrderStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderStatusPage.pageTitleCreate);
    });

    it('should create order status and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrderStatus', baseContext);

      const textResult = await addOrderStatusPage.setOrderStatus(page, createOrderStatusData);
      await expect(textResult).to.contains(statusesPage.successfulCreationMessage);

      const numberOfLinesAfterCreation = await statusesPage.getNumberOfElementInGrid(page, tableName);
      await expect(numberOfLinesAfterCreation).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 2 - Check the new status in order page
  describe('Check the existence of the new status in the order page', async () => {
    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await statusesPage.goToSubMenu(
        page,
        statusesPage.ordersParentLink,
        statusesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderBasePage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
    });

    it(`should check if the order status '${createOrderStatusData.name}' is visible`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDoesStatusVisible', baseContext);

      const isStatusExist = await viewOrderBasePage.doesStatusExist(page, createOrderStatusData.name);
      await expect(isStatusExist, 'Status does not exist').to.be.true;
    });
  });

  // 3 - Update order status
  describe('Update order status created', async () => {
    it('should go to \'Shop Parameters > Order Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPageToUpdate', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.orderSettingsLink,
      );

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });

    it('should go to \'Statuses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStatusesPageToUpdate', baseContext);

      await orderSettingsPage.goToStatusesPage(page);

      const pageTitle = await statusesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(statusesPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await statusesPage.resetFilter(page, tableName);
      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        createOrderStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name');
      await expect(textEmail).to.contains(createOrderStatusData.name);
    });

    it('should go to edit order status page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditOrderStatusPage', baseContext);

      await statusesPage.goToEditPage(page, tableName, 1);

      const pageTitle = await addOrderStatusPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderStatusPage.pageTitleEdit(createOrderStatusData.name));
    });

    it('should update order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await addOrderStatusPage.setOrderStatus(page, editOrderStatusData);
      await expect(textResult).to.contains(statusesPage.successfulUpdateMessage);

      const numberOfOrderStatusesAfterUpdate = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfOrderStatusesAfterUpdate).to.be.equal(numberOfOrderStatuses + 1);
    });
  });

  // 4 - Delete order status
  describe('Delete order status', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await statusesPage.resetFilter(page, tableName);
      await statusesPage.filterTable(
        page,
        tableName,
        'input',
        'name',
        editOrderStatusData.name,
      );

      const textEmail = await statusesPage.getTextColumn(page, tableName, 1, 'name');
      await expect(textEmail).to.contains(editOrderStatusData.name);
    });

    it('should delete order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderStatus', baseContext);

      const textResult = await statusesPage.deleteOrderStatus(page, tableName, 1);
      await expect(textResult).to.contains(statusesPage.successfulDeleteMessage);

      const numberOfOrderStatusesAfterDelete = await statusesPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfOrderStatusesAfterDelete).to.be.equal(numberOfOrderStatuses);
    });
  });
});
