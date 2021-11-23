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
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const OrderStatusFaker = require('@data/faker/orderStatus');

const baseContext = 'functional_BO_shopParameters_orderSettings_statuses_CRUDOrderStatus';

let browserContext;
let page;
let numberOfOrderStatuses = 0;
const tableName = 'order';

const createOrderStatusData = new OrderStatusFaker();
const editOrderStatusData = new OrderStatusFaker({name: `edit_${createOrderStatusData.name}`});

/*
Create new order status
View new status in order page
Update order status
Delete order status
 */
describe('BO - Shop Parameters - Order Settings - Statuses : Create, read, update and '
  + 'delete order status in BO', async () => {
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

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should check if the order status '${createOrderStatusData.name}' is visible`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDoesStatusVisible', baseContext);

      const isStatusExist = await viewOrderPage.doesStatusExist(page, createOrderStatusData.name);
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
      await expect(pageTitle).to.contains(addOrderStatusPage.pageTitleEdit);
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
